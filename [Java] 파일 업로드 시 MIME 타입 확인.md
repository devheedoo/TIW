# [Java] 파일 업로드 시 MIME 타입 확인

파일 업로드 관련하여 정부의 보안 강화 지침에 따라 파일 업로드 시 MIME 타입 확인하는 기능을 추가했다.

참고로 나모 크로스에디터 기술팀에 문의해봤는데 에디터에서 파일 업로드 시에는 MIME 타입을 확인하지 않고 파일명에 적힌 확장자로만 확인한다고 한다. 그래서 에디터에서 업로드 기능을 모두 비활성화했다.

Java SE에서 기본적으로 제공하는 `Files.probeContentType(PATH)`  메소드는 파일을 업로드 한 후 MIME 타입을 확인하는 방식이다. 업로드 전에 확인하기 위해서 Tika 라이브러리를 사용했다.

테스트 결과 기본적인 파일에 대해서는 무난하게 진행했다. 하지만 특정 회사의 프로그램에서 작성한 파일에 대한 MIME 타입을 `application/x-tika-*` 와 같이 반환해서 조금 당황했다.

- 한글 파일의 경우 `application/x-hwp`, `applicaion/haansofthwp`로 알려져 있는데 `application/x-tika-msoffice`를 반환한다.
- Office 2010 버전에서 작성한 docx, pptx, xlsx 확장자 파일의 경우 `application/x-tika-ooxml`를 반환한다.

자주 사용하는 타입이어서 기능 구현 시 허용하는 MIME 타입 목록에 일단 추가했다. 추후에 타입 확인을 더 명확하게 할 수 있도록 기능 개선이 필요한 것 같다.

## 진행과정

1. Maven dependency 추가
2. 파일 업로드 컨트롤러 수정

### 1. Maven dependency 추가

```xml
<!-- Tika: Check MIME type of files -->
<dependency>
    <groupId>org.apache.tika</groupId>
    <artifactId>tika-core</artifactId>
    <version>1.14</version>
</dependency>
```

### 2. 파일 업로드 컨트롤러 수정

**Tika**를 사용하면 파일 업로드 전 `inputStream`을 이용해 MIME 타입을 확인할 수 있다.

```java
public Map fileUpload(MultipartFile file) {
    // (중략)
    Input Stream inputStream;
    try {
        inputStream = file.getInputStream();
        Tika tika = new Tika();
        String mimeType = tika.detect(inputStream);
        
        // 허용하는 MIME 타입인지 확인
        if (!isAllowedMIMEType(mimeType)) {
            // 오류 로그 출력 (중략)
        } else {
            // 파일 업로드 과정 진행 (중략)
        }
    }
}
```

아래는 허용하는 MIME 타입 목록에 포함되는지 확인하는 함수다. 일반적으로 알려진 MIME 타입명을 조건으로 작성했다가 **Tika**에서 반환해주는 값이 달라 일부를 추가/수정했다.

```java
private boolean isAllowedMIMEType(String mimeType) {

    if (mimeType == null || mimeType.equals("")) return false;
    
    String[] allowedMIMETypesEquals = {
        "application/zip",    // .zip
        "application/pdf",    // .pdf
        "application/msword", // .doc, .dot
        "application/x-hwp", "applicaion/haansofthwp", "application/x-tika-msoffice", // .hwp
        "application/x-tika-ooxml"  // .xlsx, .pptx, .docx
    };
    for (int i=0; i<allowedMIMETypesEquals.length; i++) {
        if (mimeType.equals(allowedMIMETypesEquals[i])) {
            return true;
        }
    }
    
    String[] allowedMIMETypesStartsWith = {
        "image",    // .png, .jpg, .jpeg, .gif, .bmp
        "text",     // .txt, .html 등
        "application/vnd.ms-word",          // .docx 등 워드 관련
        "application/vnd.ms-excel",         // .xls 등 엑셀 관련
        "application/vnd.ms-powerpoint",    // .ppt 등 파워포인트 관련
        "application/vnd.openxmlformats-officedocument",    // .docx, .dotx, .xlsx, .xltx, .pptx, .potx, .ppsx
        "applicaion/vnd.hancom"     // .hwp 관련
    };
    for (int i=0; i<allowedMIMETypesStartsWith.length; i++) {
        if (mimeType.startsWith(allowedMIMETypesStartsWith[i])) {
            return true;
        }
    }
    
    return false;
}
```

## 참고자료

- Apache Tika 0.9 테스트 결과 목록, https://issues.jboss.org/browse/EXOJCR-1378
- MIME type check using tika jars, https://stackoverflow.com/questions/22225813/mimetype-check-using-tika-jars
- Complete list of MIME types, https://developer.mozilla.org/ko/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
