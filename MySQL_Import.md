

### MySQL Import

#### 사용자, 데이터베이스 생성

MySQL 루트 계정으로 접속하여 Import할 사용자, 데이터베이스 생성

> Export할 때의 사용자, 데이터베이스명과 동일하게 진행한다.

```bash
# 관리자 권한으로 MySQL 접속
$ mysql -uroot -p
```

```mysql
-- 사용자 생성
CREATE USER 'USER_NAME'@'localhost' IDENTIFIED BY 'PASSWORD'
CREATE USER 'USER_NAME'@'%' IDENTIFIED BY 'PASSWORD'
-- 사용자 조회
SELECT Host, User FROM USER;

-- 데이터베이스 생성
CREATE DATABASE DATABASE_NAME;

-- 사용자 권한 부여(사용할 데이터베이스에 대해서만)
GRANT ALL PRIVIELEGS ON 'DATABASE_NAME'.* TO 'USER_NAME'@'localhost' IDENTIFIED BY 'PASSWORD';
GRANT ALL PRIVIELEGS ON 'DATABASE_NAME'.* TO 'USER_NAME'@'%' IDENTIFIED BY 'PASSWORD';
-- 권한 부여 변경사항 적용
FLUSH PRIVILEGES;
```



#### sql 파일 이용해 Import

```bash
# Import 시도(실패)
$ mysql -u USER_NAME -p -D DATABASE_NAME < ./dbdump.sql > ./dbdump.log 2>&1
```

```
ERROR at line 164: ASCII '\0' appeared in the statement, but this is not allowed unless option --binary-mode is enabled and mysql is run in non-interactive mode. Set --binary-mode to 1 if ASCII '\0' is expected. Query: 'INSERT INTO `TABLE_NAME` VALUES ( ...
```

`--binary-mode` 옵션 추가했지만 다른 에러 발생

```bash
# Import 시도(실패)
$ mysql -u USER_NAME -p -D DATABASE_NAME --binary-mode < ./dbdump.sql > ./dbdump.log 2>&1
```

```
ERROR 1064 (42000) at line 165: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ''/usr/local/...' at line 1
```

쉽사리 해결되지 않아 **MySQL Workbench** 프로그램을 이용하기로 했다.

1. MySQL Workbench에서 접속 후 Server > Data Import 메뉴 선택
2. Import from Disk 탭에서 Import Options > Import from Self-Contained File 체크
3. 우측의 [...] 버튼 클릭해서 mysqldump로 생성한 sql 파일 선택
4. dump 시 사용한 스키마명과 타겟 스키마명이 같으므로 Default Target Schema 따로 선택하지 않고 바로 우측 하단의 [Start Import] 버튼 클릭

```
you need (at least one of) the super privilege(s) for this operation mysql
```

위와 같은 에러가 발생할 경우 MySQL root 권한으로 접속해서 사용자 권한 추가

```mysql
GRANT USAGE ON *.* TO 'USER_NAME'@'localhost'
GRANT USAGE ON *.* TO 'USER_NAME'@'%'
```





테스트 중 아래 에러 발생

```
com.mysql.jdbc.exceptions.jdbc4.MySQLSyntaxErrorException: Expression #2 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'DATABASE_NAME.TABLE_NAME.COLUMN_NAME' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by
```

`only_full_group_by` 옵션 끄기

<https://tableplus.io/blog/2018/08/mysql-how-to-turn-off-only-full-group-by.html>

```mysql
-- 기존 sql_mode 값: ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
-- ONLY_FULL_GROUP_BY 항목만 제거
SET GLOBAL sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
```
