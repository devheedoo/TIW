

## Windows10-Apache2.4-PHP5.6_설치

### Visual C Runtime 설치

[PHP는 Visual C Runtime(CRT)을 필요로 한다.](<https://www.php.net/manual/en/install.windows.requirements.php>)

- PHP 5.5, 5.6: VC CRT 11
- PHP 7.0+: VC CRT 14

구글에서 Visual C Runtime 11을 검색하면 맨 위에 [마이크로소프트 다운로드 링크](https://www.microsoft.com/ko-kr/download/details.aspx?id=30679)가 나타나므로 여기서 다운로드 후 설치

> [Visual C Runtime 공식 다운로드 페이지](<https://support.microsoft.com/ko-kr/help/2977003/the-latest-supported-visual-c-downloads>)를 들어가보면 VC11은 **Visual Studio 2012**에 해당한다는 걸 알 수 있다. 하지만 해당 페이지의 링크로 들어가 다운받으려고 하면 Visual Studio를 구독하라고 나온다.

### Apache 설치

[Apache Lounge 링크](<https://www.apachelounge.com/download/VC11/>)에서 Apache 2.4 VC11용 버전을 다운로드한다.

> 2019-04-24 기준 최신 설치파일은 httpd-2.4.38-win64-VC11.zip

압축 푼 다음 `C:\Apache24` 로 경로 변경

`C:\Apache24\conf\httpd.conf` 파일 수정

```
ServerName localhost:80
```

cmd 관리자 권한으로 실행 후

```bash
> C:\Apache24\bin\httpd.exe -k install
```

브라우저 주소에 `localhost`를 입력해서 확인

### PHP 설치

5.6 버전은 오래 되어 [Archives 페이지](<https://windows.php.net/downloads/releases/archives/>)로 넘어갔다. zip 파일을 다운로드

압축 푼 다음 `C:\php56`로 경로 변경

`C:\php56\php.ini-development` 파일을 복사 후 `php.ini`로 이름 변경 후 파일 수정

```ini
short_open_tag = On
error_reporting = E_ALL
display_erros = On
extension_dir = "C:\php56\ext"

# 아래 항목들 중 골라서 주석 제거(모르겠으면 다 제거)
extension=phpbz2.dll
...
extension=php_shmop.dll

date.timezone = Asia/seoul
```

`php.ini` 파일을 `C:\Windows\`, `C:\Windows\System32\` 폴더로 복사

`C:\Apache24\conf\httpd.conf` 파일 수정

```properties
# IfModule 안쪽 부분 수정
<IfModule dir_module>
	DirectoryIndex index.php index.html index.htm
</IfModule>

# AddType 부분 아래에 추가
AddType application/x-httpd-php .php .inc .bak .old .c

# 맨 마지막 줄에 추가
# BEGIN PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL
PHPIniDir "C:\php56\"
LoadModule php5_module "C:\php56\php5apache2_4.dll"
# END PHP INSTALLER EDITS - REMOVE ONLY ON UNINSTALL
```

`C:\Apache24\htdocs\` 폴더에 아래 텍스트 입력해서 `index.php` 파일 생성

```php
<?php
	phpinfo();
?>
```

cmd 관리자 권한으로 실행 후

```bash
C:\Apache24\bin\httpd.exe -k stop
C:\Apache24\bin\httpd.exe -k start
```

브라우저 주소에 `localhost`를 입력해서 확인
