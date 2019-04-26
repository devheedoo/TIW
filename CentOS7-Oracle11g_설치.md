## 설치 환경
- OS : CentOS Linux release 7.6.1810 (Core)
- Vendor : Oracle
- Version : 11.2.0.1.0
- Port : 8421
- SID : oracle11g
- 계정 정보 : oracle / oracle
- 설치경로 : /app/oracle/product/11.2.0.1

## 설치과정

### 기본 참고 자료

- [ORACLE 설치의 정석1 - DB엔진편 ( CentOS6.7 & Oracle DB 11.2.0.4 )](https://allroundplaying.tistory.com/13)
- [ORACLE 설치의 정석2 - 리스너 & DB편 ( CentOS6.7 & Oracle DB 11.2.0.4 )](https://allroundplaying.tistory.com/17)

### 이후 추가 과정

#### 인코딩 설정 변경

설치 과정 중에 인코딩을 한글로 설정해도 Oracle 인코딩 파라미터가 영어로 되어있었다. sysdba 계정으로 sqlplus 접속 후 인코딩 관련 파라미터들을 직접 수정하고 재시작하는 과정이 필요하다.

```sql
-- 현재 인코딩 파라미터 조회
> column parameter format a30;
> column value format a30;
> select * from nls_database_parameters;

-- 기존 인코딩 파라미터와 일치하도록 변경 (기존 210.118.0.11 서버의 Oracle 설정처럼 수정)
> update props$ set value$='VALUE_TO_BE' where name='COLUMN_NAME';

-- 수정사항 반영 후 재시작
> commit;
> shutdown immediate;
> startup;

-- 결과 조회
> select * from nls_database_parameters;
```

#### session 최댓값 증가시키기

session 개수 -> connection pool의 connection 최대 개수

```sql
-- 세션 개수 조회
column name format a35;
column type format a10;
column value format a10;
show parameter sessions;

-- 세션 개수 변경 (DB 재시작 필요)
alter system set sessions=512 scope=spfile;
```

```bash
# Oracle DB 재시작
$ dbshut oracle11g
$ dbstart oracle11g
```


#### 포트 변경: 1521 -> 8421

```bash
# $ORACLE_HOME/network/admin/listener.ora, tnsnames.ora 파일 수정해도 잘 적용 안 된다.
# 다시 리스너 설치 프로그램을 이용하기로 했다. (Xming 사용)
$ export DISPLAY=MY_IP_ADDRESS:0.0;
$ netca
# 옵션에서 reconfiguration 선택 후 포트 변경해도 적용 안 됐다.
# 옵션에서 delete 선택해서 제거 후, 8421 포트로 다시 설치했더니 바로 성공했다.

# 확인
$ netstat -nap | grep LISTEN | grep :8421
```

#### 서버 재부팅 시 자동실행 설정

이 [링크1](https://wookoa.tistory.com/213)를 참고해서 진행하되 서비스 파일 경로는 이 [링크2](https://hadafq8.wordpress.com/2016/03/05/rhel-7oel-7centos-7-configuring-automatic-startup-of-oracle-db-under-systemd/)처럼 `/etc/systemd/system/`으로 사용했다.

> $ lsnrctl start [LISTENER_NAME]
>
> - LISTENER_NAME 항목을 넣지 않으면 기본 1521 포트로 리스너를 실행한다.
>
> 참고: [https://dba.stackexchange.com/a/160268](https://dba.stackexchange.com/a/160268)
>
> $ dbstart, dbshut [ORACLE_SID]
>
> - 얘도 파라미터를 설정해줘서 추후에 SID를 추가할 때 실수가 없도록 한다.
