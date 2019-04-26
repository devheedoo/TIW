- 운영체제 버전: CentOS 7

```bash
$ grep . /etc/*-release
/etc/centos-release:CentOS Linux release 7.6.1810 (Core)
/etc/os-release:NAME="CentOS Linux"
/etc/os-release:VERSION="7 (Core)"
/etc/os-release:ID="centos"
/etc/os-release:ID_LIKE="rhel fedora"
/etc/os-release:VERSION_ID="7"
/etc/os-release:PRETTY_NAME="CentOS Linux 7 (Core)"
/etc/os-release:ANSI_COLOR="0;31"
/etc/os-release:CPE_NAME="cpe:/o:centos:centos:7"
/etc/os-release:HOME_URL="https://www.centos.org/"
/etc/os-release:BUG_REPORT_URL="https://bugs.centos.org/"
/etc/os-release:CENTOS_MANTISBT_PROJECT="CentOS-7"
/etc/os-release:CENTOS_MANTISBT_PROJECT_VERSION="7"
/etc/os-release:REDHAT_SUPPORT_PRODUCT="centos"
/etc/os-release:REDHAT_SUPPORT_PRODUCT_VERSION="7"
/etc/redhat-release:CentOS Linux release 7.6.1810 (Core)
/etc/system-release:CentOS Linux release 7.6.1810 (Core)
```

## Oracle 설치

- CentOS 서버의 Oracle용 계정: oracle/oracle
- Oracle 설치 경로: /app/oracle/product/11.2.0.1
- Oracle 버전: 11.2.0.1
- SID: oracle11g
- Oracle 관리자 계정: sys as sysdba/oracle11g

Oracle 기초 명령어

```bash
# 리눅스 oracle 계정으로 전환
$ su - oracle

# Oracle 실행/종료 (oracle 계정으로 실행해야 함)
$ sqlplus '/as sysdba'
> startup
> shutdown

# 리스너 실행/종료 (oracle 계정으로 실행해야 함)
$ lsnrctl start
$ lsnrctl stop
```

> 설치 시 참고한 글 목록
>
> - [ORACLE 설치의 정석1 - DB엔진편 ( CentOS6.7 & Oracle DB 11.2.0.4 )](https://allroundplaying.tistory.com/13)
> - [ORACLE 설치의 정석2 - 리스너 & DB편 ( CentOS6.7 & Oracle DB 11.2.0.4 )](https://allroundplaying.tistory.com/17)



### 인코딩 변경

sysdba 계정으로 sqlplus 접속 후 인코딩 항목들을 수정한다.

```sql
-- 현재 인코딩 파라미터 조회
> column parameter format a30;
> column value format a30;
> select * from nls_database_parameters;

-- 기존 인코딩 파라미터와 일치하도록 변경
> update props$ set value$='VALUE_TO_BE' where name='COLUMN_NAME';

-- 수정사항 반영 후 재시작
> commit;
> shutdown immediate;
> startup;

-- 결과 조회
> select * from nls_database_parameters;
```

### 포트 변경: 1521 -> 8421

```bash
# $ORACLE_HOME/network/admin/listener.ora, tnsnames.ora 파일 수정해도 잘 적용 안 된다.
# 다시 리스너 설치 프로그램을 이용하기로 했다.
$ export DISPLAY=210.118.0.16:0.0;
$ netca
# 옵션에서 reconfiguration 선택 후 포트 변경해도 적용 안 됐다.
# 옵션에서 delete 선택해서 제거 후, 8421 포트로 다시 설치했더니 바로 성공했다.

# 확인
$ netstat -nap | grep LISTEN | grep :8421
```

### 재시작 시 자동실행 설정

<https://wookoa.tistory.com/213>

위 링크 따라하되

<https://hadafq8.wordpress.com/2016/03/05/rhel-7oel-7centos-7-configuring-automatic-startup-of-oracle-db-under-systemd/>

서비스 파일 경로는 위 링크처럼 /etc/systemd/system/ 사용



> $ lsnrctl start [LISTENER_NAME]
>
> - LISTENER_NAME 항목을 넣지 않으면 기본 1521 포트로 리스너를 실행한다.
>
> https://dba.stackexchange.com/a/160268
>
> $ dbstart, dbshut [ORACLE_SID]
>
> - 얘도 넣어주자.
