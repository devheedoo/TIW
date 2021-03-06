# CentOS7-Oracle11g 설치

Oracle 11.2.0.1.0 (Oracle 11g)

## 설치 환경
- OS : CentOS Linux release 7.6.1810 (Core)
- Vendor : Oracle
- Port : 8421
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

$ORACLE_HOME/network/admin/listener.ora, tnsnames.ora 파일을 수정해도 포트 변경 작업이 잘 되지 않아 `netca`를 다시 실행해서 해결했다.

PC에서 Xming 프로그램을 실행한 후, 

```bash
$ export DISPLAY=MY_IP_ADDRESS:0.0;
$ netca
```

옵션에서 reconfiguration 말고 delete를 선택해서 제거 후 8421포트로 새로 설치한다.

```
# 확인
$ netstat -nap | grep LISTEN | grep :8421
```

만약 외부에서 DB 연결이 되지 않을 경우 리스너 상태를 확인한다.

```
$ lsnrctl status [LISTENER_NAME]
```

결과 중에 `the listener supports no services.` 문구가 보일 경우 리스너가 Oracle DB 인스턴스와 연결되지 않은 것이다.

이 경우 직접 $ORACLE_HOME/network/admin/listener.ora 파일을 수정한다. 파일 상단에 아래 내용을 추가한 후,

```
SID_LIST_[LISTENER_NAME] =
  (SID_LIST =
    (SID_DESC =
      (SID_NAME = [SID_NAME])
      (ORACLE_HOME = [ORACLE_HOME_PATH])
    )
  )
```

리스너만 재시작하면 된다.

```
$ lsnrctl stop [LISTENER_NAME]
$ lsnrctl start [LISTENER_NAME]
```

결과에 `Service [INSTANCE_NAME] has 1 instance(s).` 와 같은 문구가 보이면 성공이다.


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
