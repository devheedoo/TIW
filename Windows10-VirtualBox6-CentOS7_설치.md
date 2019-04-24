### VirtualBox 설치

[https://yoogomja.tistory.com/entry/CentOS-VirtualBox%EB%A1%9C-CentOS7-%EC%84%A4%EC%B9%98%ED%95%98%EA%B8%B0](https://yoogomja.tistory.com/entry/CentOS-VirtualBox로-CentOS7-설치하기)

Virtualbox 최신버전 설치 후 실행

파일 - 환경설정 - 입력 - 가상머신 탭 - "호스트 키 조합" 단축키를 우측 Ctrl 버튼 왼쪽의 Application 버튼으로 변경

기본 설정인 우측 Ctrl로 할 경우 잘 동작하지 않았다.

### CentOS 설치

[CentOS ISO 다운로드 링크](<https://www.centos.org/download/>)에서 DVD 또는 Minimal ISO 다운로드

새로 만들기

- 이름: **CentOS7**
- 종류: **Linux**
- 버전: **Red Hat (64-bit)**

각자 설정에 맞게 진행해서 생성

생성한 가상머신 설정 - 저장소 - 컨트롤러: IDE 클릭하여 아까 다운로드한 **CentOS ISO 파일**을 선택

가상머신 실행

1. **Install CentOS Linux 7** 선택
2. 언어 **Korean** 검색 후 선택
3. 설치 요약: 아래 3가지 설정 후 로딩 기다렸다가 우측 하단의 [설치 시작]
    1. 소프트웨어 선택: **"서버 - GUI 사용"**, 나머지는 각자 환경에 맞게 선택 후 좌측 상단의 [완료]
    2. 설치 대상: 클릭 후 바로 좌측 상단의 [완료]
    3. 네트워크 및 호스트명: 우측 상단의 이더넷을 **"켬"** 상태로 변경 후 좌측 상단의 [완료]
4. ROOT 암호 설정
5. 완료 후 재부팅한 다음 라이센스 동의

#### 게스트 확장 설치

<https://webdevnovice.tistory.com/3>

이 과정을 진행해야 화면 크기도 자동으로 바뀌고 클립보드 사용도 가능하다.

설치 전 필요 패키지 설치

```bash
$ yum install gcc make kernel-devel kernel sources kernel-headers
$ yum -y groupinstall "Development Tools"
$ reboot
```

가상머신 창에서 상단 메뉴 - 장치 - **게스트 확장 설치** 선택

완료 후 재부팅

가상머신 창에서 상단 메뉴 - 장치 - 클립보드 공유 - **양방향** 선택

가상머신 창에서 상단 메뉴 - 장치 - 드래그 앤 드롭 - **양방향** 선택

##### VirtualBox CentOS 재부팅 시 오류

> drm:vmw_host_log[vmwgfx] *ERROR* Failed to send host log message

위 문구 발생 후 무한로딩이 발생한다. VirtualBox의 CentOS 설정을 아래와 같이 변경하면 해결.

Settings - Display - Graphics Controller: **VBoxSVGA** 로 변경
