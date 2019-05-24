# Apache-PHP 작업환경 구축

Windows PC에 Apache-PHP 프로젝트 작업환경을 설정하는 방법이다.

## 과정

### 1. SVN/Git Checkout

D:\workspace\[프로젝트명] 폴더를 생성하고, Checkout 한다.

### 2. VC CRT 설치

PHP, Apache 버전에 맞는 vcredist_xx.exe 설치한다.

### 3. PHP 설치

php-xx.zip 압축 풀어서 폴더째로 C:\로 이동시킨 후 폴더명을 phpxx로 변경한다.

다음 파일을 환경에 맞게 수정한다:

- C:\phpxx\php.ini

### 4. Apache 설치

httpd-xx.zip 압축 풀어서 Apachexx 폴더를 C:\로 이동시킨다.

다음 2개 파일을 환경에 맞게 수정한다:

- C:\Apachexx\conf\httpd.conf
- C:\Apachexx\conf\extra\httpd-vhosts.conf

#### 윈도우 서비스 등록

Apache를 윈도우 서비스로 등록한다:

1. 명령 프롬프트(cmd)를 관리자 권한으로 실행한다.

2. 아파치 실행파일 경로로 이동한다.

   ```bash
   > cd C:\Apachexx\bin
   ```

3. 서비스 등록 및 실행 명령어를 입력한다.

   ```bash
   > httpd.exe -k install -n "Apache xx"
   ```

### 5. 테스트

웹 브라우저에서 `http://localhost:80` 또는 `http://127.0.0.1:80` 접속하여 잘 설치됐는지 확인한다.
