# PHP 프로젝트 리눅스에서 윈도우로 복사하기

> Windows 10 Pro, Apache 2.4.23, PHP 5.4.13, MySQL 5.1.52

## 데이터베이스 복사

웹서버와 소스를 먼저 이관하더라도 DB가 없으면 홈페이지 첫 화면부터 안 뜰 수도 있다. DB부터 복사한다. 먼저 실서버 버전을 확인한다.

### MySQL 버전 확인

```bash
$ mysql --version
```

실서버는 5.1 이었지만 버전이 조금 높아도 크게 문제 없으리라 짐작하고 MySQL 5.6에 복사했다. 복사 과정은 다음과 같다.

### 실서버 DB 백업

```bash
$ mysqldump -u [USER_ID] -p [PASSWORD] --databases [DATABASE_NAME] > BACKUP_FILE_NAME.sql
```

실서버에 생성된 백업 `.sql` 파일을 테스트 서버로 옮긴다.

> 함수, 트리거, 인덱스, 외래키 등이 사용되었을 경우 위 명령어보다 복잡하게 진행해야 할 수도 있다.

### 테스트서버 DB에 Import

#### 테스트 DB 접속

```bash
$ mysql -u root -p
```

#### 데이터베이스, 계정 생성

> 별도의 DB 서버를 운영 중인 경우 `@'localhost` 계정과 `@'%'` 계정을 모두 만들어야 내부, 외부에서 모두 접속할 수 있다.

```mysql
-- 데이터베이스 생성
CREATE DATABASE [DATABASE_NAME]

-- 내부 접속 계정 생성, 권한 부여
CREATE USER '[USER_NAME]'@'localhost' IDENTIFIED BY '[PASSWORD]';
GRANT ALL PRIVILEGES ON [DATABASE_NAME].* TO '[USER_NAME]'@'localhost';

-- 외부 접속 계정 생성, 권한 부여
CREATE USER '[USER_NAME]'@'%' IDENTIFIED BY '[PASSWORD]';
GRANT ALL PRIVILEGES ON [DATABASE_NAME].* TO '[USER_NAME]'@'%';

-- 권한 변경사항 업데이트
FLUSH PRIVILEGES;
```

#### MySQL Workbench 이용해 Import

MySQL Workbench 프로그램을 이용해 웹서버 PC에서 접속이 되는지 확인한다.

정상적으로 연결되면 Import 과정을 진행한다.

1. MySQL Workbench에서 접속 후 Server > Data Import 메뉴 선택
2. Import from Disk 탭에서 Import Options > Import from Self-Contained File 체크
3. 우측의 [...] 버튼 클릭해서 mysqldump로 생성한 sql 파일 선택
4. dump 시 사용한 스키마명과 타겟 스키마명이 같으므로 Default Target Schema 따로 선택하지 않고 바로 우측 하단의 [Start Import] 버튼 클릭

### sql_mode 설정 복사

가능하면 실서버 DB의 `sql_mode` 설정을 똑같이 테스트 DB에 적용한다. 이 설정을 해주지 않으면 처음 DB 연결은 되지만 특정 기능 사용 시 오류가 발생할 수 있다.

MySQL 설정 확인:

```mysql
$ SELECT @@sql_mode;
```

**my.cnf** 파일에 다음과 같이 코드를 추가하거나 수정해주면 된다. 예를 들면 아래와 같다.

```
[mysqld] 
# sql_mode
sql_mode=NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION
```

이후 DB를 재시작한다.

```bash
$ systemctl restart mysqld
```



## 프로젝트 소스 복사

너무 큰 이미지, 동영상, 문서 파일은 걸러내고 전체 코드를 복사한다.

또한 코드 전체 검색으로 실서버의 IP나 실운영중인 URL이 사용된 부분을 찾아 테스트서버에 맞게 변경해준다.

예를 들면 DB 설정 변경하는 부분이 있다. 최근 많은 MySQL 서버는 보안을 위해 기본 포트를 사용하지 않고 별도의 포트를 지정해서 사용한다. 이 떄문에 **database.php** 라는 설정 파일에 다음 코드를 추가했다.

```php
$db['default']['port'] = 5522;
```

그런데 자꾸 접속 오류가 발생하는 것이다. 구글링 끝에 `mysql_connect()` 라는 함수를 사용할 때는 아래와 같이 첫 번째 파라미터인 `HOSTNAME` 부분에 `HOSTNAME:PORT_NUMBER` 와 같이 설정해줘야 한다는 것을 알았다.

```php
// 기존 코드 (MySQL 기본 포트인 3306으로 연결됨)
// $conn = mysql_connect($db['hostname'].':'.$db['port'], $db['username'], $db['password']);

// 변경된 코드 (직접 설정한 포트로 연결됨)
$conn = mysql_connect($db['hostname'].':'.$db['port'], $db['username'], $db['password']);
```



## Apache 설치

### 설치에 앞서 버전 선택하기

실서버와 동일한 환경을 제공하기 위해 같은 버전을 다운로드 받는다.

Apache 버전 확인:

```bash
$ httpd -v
```

PHP가 Win32 버전일 경우 Apache도 Win32 버전이어야 함을 주의하고, 해당 버전에 맞는 Apache-PHP 연동 모듈(php5module2_4.dll 과 같은) 파일을 반드시 다운받아야 한다. 또한 PHP 버전에 맞는 VC 버전이 있는데, 이에 맞는 Apache 설치파일을 다운받는다.

> 참고: https://www.php.net/manual/en/install.windows.requirements.php

예를 들어 이번 작업에 사용한 설치 파일명은 다음과 같다.

- httpd-2.4.23-win32.zip (VC10)
- php-5.4.13-Win32-VC9-x86.zip (VC10과도 호환됨)
- vcredist_x86.exe (VC10)

버전에 맞는 VC는 구글에 **VC9**와 같이 검색하면 바로 다운로드 링크를 찾을 수 있다.

### Apache 설치 및 환경설정

httpd-2.4.23-win32.zip 압축을 풀면 안에 Apache24 폴더가 있다. `C:\Apache24` 에 위치하도록 옮긴다. 다른 경로도 괜찮지만 윈도우에 설치하는 경우 보통 다 이 경로에 하는 것 같다.

수정해야 할 파일은 **C:\Apache24\conf\httpd.conf** 이다. 실서버 **httpd.conf** 파일을 복사해서 붙여넣은 후 테스트서버에 맞게 변경한다. 보통 변경하는 항목은 다음과 같다.

- ServerName
- DocumentRoot, Directory 경로
- ErrorLog 경로

이번 작업의 경우 리눅스에서 윈도우로 이동했기 때문에 Apache와 PHP를 연결해주는 모듈이 변경되었다. 아래 코드가 이 부분에 해당한다. 기존의 php5_module 부분을 제거하고 아래 코드를 추가한다.

```ini
PHPIniDir "C:/php54"
LoadModule php5_module "C:/php54/php5apache2_4.dll"
```

추가로 **httpd-vhosts.conf** 파일을 사용 중이어서 해당 파일 내의 디렉토리 설정을 변경했고, **httpd-ssl.conf** 설정을 통해 HTTPS를 사용중이어서 테스트에서는 사용하지 않기 위해 **httpd.conf** 에서 아래 코드를 주석처리헀다.

```ini
# LoadModule ssl_module modules/mod_ssl.so
```



## PHP 설치

### 압축 해제 후 파일 이동

php-5.4.13-Win32-VC9-x86.zip 압축을 풀면 폴더 내에 내용이 많다.  `C:\php54` 폴더를 생성한 후 모두 이 안으로 이동시킨다.

### php.ini 복사 및 수정

수정할 파일은 **php.ini-development**이다. 원래는 이 파일을 **php.ini** 로 이름을 변경하여 설정을 수정해준다. 하지만 우리는 이관하는 작업 중이기 때문에 실서버의 **php.ini** 파일을 복사하면 된다.

리눅스에서 리눅스로 이관할 경우 경로만 변경하면 되지만, 리눅스에서 윈도우로 이관하기 때문에 추가로 PHP 모듈 사용 부분을 수정해야 한다.

주석에 적힌 설명에서 볼 수 있듯이, `.so` 파일 대신 `.dll` 파일을 모듈로 사용해야 한다.

```ini
; If you wish to have an extension loaded automatically, use the following
; syntax:
;
;   extension=modulename.extension
;
; For example, on Windows:
;
;   extension=msql.dll
;
; ... or under UNIX:
;
;   extension=msql.so
```

기존의 `.so` 파일이 적힌 코드들을 주석 처리하고, 아래와 같은 코드들을 주석 해제한다.

```ini
extension=php_mbstring.dll
extension=php_mysql.dll
```

### php_module 파일 이동

다운받았던 **php5apache2_4.dll** 파일을 Apache 설정에서 적었던 경로에 맞게 `C:\php54` 폴더로 옮긴다.



## Apache 서비스 등록 및 테스트

이제 Apache를 서비스로 등록한다. 관리자 권한으로 명령 프롬프트(cmd)를 실행시킨다.

```bash
> httpd.exe -k install -n "Apache 2.4.23"
```

서비스를 등록하면 바로 실행된다. 설정이 잘못될 경우 오류가 발생할 수도 있다.

localhost:80 에 접속하면 프로젝트를 확인할 수 있다.







