### CentOS Java 설치

<https://blog.hanumoka.net/2018/04/30/centOs-20180430-centos-install-jdk/>

#### Java 설치

```bash
# 설치가능한 Java 목록 조회
$ yum list java*jdk-devel

# java1.8 설치
$ sudo yum install java-1.8.0-openjdk-devel.x86-64

# 설치 확인
$ javac -version
javac 1.8.0_212
$ rpm -qa java*jdk-devel
java-1.8.0-openjdk-devel-1.8.0.212.b04-0.el7_6.x86_64
```

#### 환경변수 설정

```bash
# JAVA_HOME 변수 확인
$ echo $JAVA_HOME

# javac 위치 확인
$ which javac
/usr/bin/javac
$ readlink -f /usr/bin/javac
/usr/lib/jvm/java-1.8.0-openjdk-1.8.0.212.b04-0.el7_6.x86_64/bin/javac

# 환경변수 설정
$ sudo vi /etc/profile
# 맨 아래줄에 아래 코드 추가
# export JAVA_HOME=/usr/lib/jvm/java-1.8.0-openjdk-1.8.0.212.b04-0.el7_6.x86_64

# 환경변수 설정 변경사항 업데이트
$ source /etc/profile

# 환경변수 확인
$ echo $JAVA_HOME
/usr/lib/jvm/java-1.8.0-openjdk-1.8.0.212.b04-0.el7_6.x86_64
$JAVA_HOME/bin/javac -version
javac 1.8.0_212
```



### CentOS7 Tomcat 설치 및 환경설정

<https://dailyworker.github.io/hello-tomcat-inLinux/>

(Tomcat 실행용 계정은 따로 만들지 않음)

Apache Tomcat 다운로드 및 설치

```bash
# Tomcat 7.0.94 tar.gz 버전 다운로드
$ sudo wget http://mirror.navercorp.com/apache/tomcat/tomcat-7/v7.0.94/bin/apache-tomcat-7.0.94.tar.gz

# 압축 풀고 원하는 경로로 이동
$ sudo tar -xvf apache-tomcat-7.0.94.tar.gz
$ sudo mkdir -p /usr/share/tomcat7
$ sudo mv apache-tomcat-7.0.94 /usr/share/tomcat7/
```

환경변수 등록

```bash
$ sudo vi /etc/profile
# 맨 아래에 아래 코드 추가
# CATALINA_HOME=/usr/share/tomcat7/apache-tomcat-7.0.94
# PATH=$PATH:$JAVA_HOME/bin:/bin:/sbin
# CLASSPATH=$JAVA_HOME/jre/lib:$JAVA_HOME/lib/tools.jar:$CATALINA_HOME/lib-jsp-api.jar:$CATALINA_HOME/lib/servlet-api.jar
# export CATALINA_HOME CLASSPATH PATH
```

Tomcat 동작 확인

```bash
$ cd /usr/share/tomcat7/apache-tomcat-7.0.94/bin
# Tomcat 실행
$ ./startup.sh
# 브라우저에서 localhost:8080 접속해서 확인

# Tomcat 종료
$ ./shutdown.sh
```

#### 방화벽 설정

가상머신이므로 일단 임시로 방화벽을 끈다. 자세한 설정은 나중에 정리.

```bash
$ sudo systemctl stop firewalld
```

#### Tomcat 전용 리눅스 계정 설정

따로 Tomcat 전용 리눅스 계정을 설정하지 않을 경우 아래와 같은 문제점이 발생한다.

1. 접근 권한이 ROOT이므로 프로그램에서 원하는 폴더에 업로드 가능
2. 파일 업로드 시 스크립트를 이요해 시스템 명령어 실행 가능
3. ? (원본 글의 내용을 이해하지 못함)

계정 설정 과정은 다음과 같다.

```bash
# 사용자 생성
$ sudo useradd -s /bin/false tomcat
# 사용자 생성 확인
$ sudo grep tomcat /etc/passwd
tomcat:x:1001:1001::/opt/tomcat:/bin/false

# 사용자 그룹 생성
$ sudo groupadd -r tomcat
# 사용자 그룹에 사용자 등록
$ sudo gpasswd -a tomcat tomcat
# 그룹 등록 확인
$ sudo grep tomcat /etc/gshadow
tomcat:!::tomcat
```

> 리눅스에서 셀 로그인 권한이 없는 계정을 만드는 방법: [nologin vs /bin/false](<https://oracle-base.com/articles/linux/linux-firewall-firewalld>)

이후 Tomcat 디렉토리와 그 하위 디렉토리의 모든 권한을 tomcat으로 변경한다.

```bash
$ cd /usr/share
$ chown -R tomcat:tomcat tomcat7
```

이하 내용은 다음에 해보고 나서 정리
