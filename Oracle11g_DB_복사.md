## Oracle11g DB 복사

1, 2번 과정은 직접 이관할 대상 Oracle에 루트 권한으로 접속해서 진행한다.

3번 과정은 소스/대상 Oracle에 접속할 수 있는 PC에서 진행한다.

### 1. 테이블스페이스 생성

테이블스페이스 생성:

```sql
create tablespace [TABLESPACE_NAME]
datafile '/app/oracle/oradata/oracle11g/[FILE_NAME].dbf' size 100m
autoextend on next 10m
maxsize unlimited;
```

테이블스페이스 조회:

```sql
select tablespace_name from dba_tablespaces;
```

### 2. 사용자 생성 및 권한 부여

사용자 생성:

```sql
create user [USER_NAME]
identified by [PASSWORD]
default tablespace [TABLESPACE_NAME]
temporary tablespace TEMP;
```

사용자 조회:

```sql
column username format a20
column default_tablespace format a20
column temporary_tablespace format a30
select username, default_tablespace, temporary_tablespace from dba_users;
select * from all_users;
```

사용자 시스템 권한 부여:

```sql
grant create session to [USER_NAME];
grant create database link to [USER_NAME];
grant create materialized view to [USER_NAME];
grant create procedure to [USER_NAME];
grant create public synonym to [USER_NAME];
grant create role to [USER_NAME];
grant create sequence to [USER_NAME];
grant create synonym to [USER_NAME];
grant create table to [USER_NAME];
grant drop any table to [USER_NAME];
grant create trigger to [USER_NAME];
grant create type to [USER_NAME];
grant create view to [USER_NAME];

-- 한 번에 해도 된다. (암호화 기능 권한 빼고)
grant create session, create database link, create materialized view, create procedure, create public synonym, create role, create sequence, create synonym, create table, drop any table, create trigger, create type, create view to [USER_NAME];
```

필요한 경우 추가 권한 부여:

```sql
-- 암호화 기능 권한 부여
grant execute on dbms_crypto to [USER_NAME];

-- 다른 사용자의 테이블 사용
grant insert, select, update, delete on [ANOTHER_USER_NAME].[TABLE_NAME] to [USER_NAME];

-- 다른 사용자의 시퀀스 사용
grant select, alter on [ANOTHER_USER_NAME].[SEQUENCE_NAME] to [USER_NAME];
```

다른 권한이 필요한 경우 다음 문서 참고해서 권한 추가

- [Database SQL Language Reference: GRANT](https://docs.oracle.com/cd/B28359_01/server.111/b28286/statements_9013.htm#SQLRF01603)

사용자 시스템 권한 확인:

```sql
column grantee format a20
column privilege format a30
select * from dba_sys_privs where grantee='[USER_NAME]';
```

사용자 역할 권한 부여:

```sql
grant exp_full_database, imp_full_database to [USER_NAME];
```

사용자 역할 권한 확인:

```sql
column grantee format a20
column granted_role format a30
select * from dba_role_privs where grantee='[USER_NAME]';
```

사용자 데이터 사용범위 제한 설정:

```sql
alter user [USER_NAME] quota unlimited on [TABLESPACE_NAME];
```

위 설정을 하지 않으면 데이터베이스 복사 과정 중 아래와 같은 오류가 발생할 수 있다.

```
객체 데이터 이동 중 TABLE_NAME
TABLE에 대해 데이터 삽입 중 오류 발생: TABLE_NAME. 500 행을 포함하는 1 일괄 처리가 실패했습니다. 
  ORA-01950: 테이블스페이스 'TABLESPACE_NAME'에 대한 권한이 없습니다.
```

### 3. sqldeveloper 프로그램 이용해서 데이터베이스 복사

**[도구 > 데이터베이스 복사]** 메뉴를 사용한다. 한 번에 복사할 경우 충돌이 발생할 수 있으므로 3단계로 나누어 진행한다.

#### 3-1. 테이블 복사

소스/대상:

- 소스 접속, 대상 접속
- 복사옵션 - 객체 복사
- DDL 복사 - 기존 대상 객체 바꾸기
- 데이터 복사 체크 해제

객체 유형:

- 표준 객체 유형 - 테이블만 체크

객체 지정:

- 조회 클릭 후 전체 선택해서 우측으로 이동

#### 3-2. 데이터를 제외한 나머지 객체 복사

소스/대상:

- 소스 접속, 대상 접속
- 복사옵션 - 객체 복사
- DDL 복사 - 기존 대상 객체 바꾸기
- 데이터 복사 체크 해제

객체 유형:

- 표준 객체 유형 - 테이블 빼고 전부 체크

객체 지정:

- 조회 클릭 후 전체 선택해서 우측으로 이동

#### 3-3. 데이터 복사

소스/대상:

- 소스 접속, 대상 접속
- 복사옵션 - 객체 복사
- DDL 복사 - 기존 대상 객체 바꾸기
- 데이터 복사 체크

객체 유형:

- 표준 객체 유형 전부 체크

객체 지정:

- 조회 클릭 후 전체 선택해서 우측으로 이동


### 4. 사용자 역할 회수(중요)

SQL Developer를 이용한 데이터베이스 복사 후 필요없는 역할을 회수한다.

이 권한을 회수하지 않으면 타 사용자의 데이터베이스를 마음대로 조회할 수 있으므로 꼭 회수해야 한다.

```sql
REVOKE exp_full_database, imp_full_databsae FROM [USER_NAME];
```
