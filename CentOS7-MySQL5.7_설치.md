### MySQL 설치

<https://opentutorials.org/module/1701/10229>

"community"는 MySQL이 유료로 변경되면서 무료 버전을 뜻하는 단어이다. 유료 버전은 "enterprise".

- CentOS 서버 root 계정으로 yum 사용해서 설치
- MySQL 설치 경로: /var/lib/mysql
- MySQL 버전: 5.7

최초 설치 시 비밀번호는 /var/log/mysqld.log 파일에 적혀있다.

```
2019-04-24T06:59:08.286400Z 1 [Note] A temporary password is generated for root@localhost: -l+0_jli/KJq
```

mysql 접속해서 root 비밀번호 변경

```bash
$ mysql -uroot -p
Enter password: -l+0_jli/KJq
```

```mysql
-- 최초 접속 시 아래 쿼리 실행
> ALTER USER 'root'@'localhost' identified by '-l+0_jli/KJq';
-- 변경사항 적용
> flush privileges;

-- 비밀번호 옵션 확인
> SHOW VARIABLES LIKE 'validate_password%';
-- 비밀번호 조건 완화
> SET GLOBAL validate_password_policy=LOW;

-- 기본 데이터베이스 선택
> use mysql;
-- root 비밀번호 변경
> UPDATE USER SET authentication_string=password('PASSWORD_TO_CHANGE') where user='root';
-- 변경사항 적용
> flush privileges;

-- 빠져나오기
> quit
```

#### 인코딩 설정 utf8로 변경

```bash
$ sudo vi /etc/my.cnf
```

아래 내용대로 수정, 없는 부분은 추가

```
[client] 
default-character-set = utf8
 
[mysql]
default-character-set=utf8
 
[mysqld]
 
datadir=/var/lib/mysql
socket=/var/lib/mysql/mysql.sock
 
character-set-server=utf8
collation-server=utf8_general_ci
init_connect=SET collation_connection = utf8_general_ci
init_connect=SET NAMES utf8
 
character-set-client-handshake = FALSE
skip-character-set-client-handshake

# Password Policy
validate_password_length=4
validate_password_policy=LOW
 
[mysqldump]
default-character-set=utf8
```

재시작 후 상태 확인

```bash
$ systemctl restart mysqld
$ mysql -uroot -p
```

```mysql
> status
--------------
mysql  Ver 14.14 Distrib 5.7.25, for Linux (x86_64) using  EditLine wrapper

Connection id:		3
Current database:	
Current user:		root@localhost
SSL:			Not in use
Current pager:		stdout
Using outfile:		''
Using delimiter:	;
Server version:		5.7.25 MySQL Community Server (GPL)
Protocol version:	10
Connection:		Localhost via UNIX socket
Server characterset:	utf8
Db     characterset:	utf8
Client characterset:	utf8
Conn.  characterset:	utf8
UNIX socket:		/var/lib/mysql/mysql.sock
Uptime:			12 sec

Threads: 1  Questions: 5  Slow queries: 0  Opens: 105  Flush tables: 1  Open tables: 98  Queries per second avg: 0.416
--------------
```

#### 실행 관련 명령어

```bash
$ systemctl [start, stop, restart, status] mysqld
```

