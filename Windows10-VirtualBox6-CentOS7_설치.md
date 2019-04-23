[링크](http://realjune.tistory.com/22) 참고해서 작업했다.

1. VirtualBox6 설치
2. CentOS7 minimal 버전 ISO 다운로드
3. VirtualBox6 에서 CentOS7 가상머신 구성
4. CentOS7 GUI 모드 설치
   1. 시스템 업데이트: `# yum update`
   2. 패키지 설치: `# yum groupinstall "X Window System" "GNOME Desktop"`
   3. GUI 모드 실행: `# startx`

> 기본 부팅모드 GUI 설정
>
> ```
> # unlink /etc/systemd/system/default.target
> # ln -sf /lib/systemd/system/graphical.target /etc/systemd/system/default.target
> ```
>
> 기본 부팅모드 CUI 설정
>
> ```
> # unlink /etc/systemd/system/default.target
> # ln -sf /lib/systemd/system/multi-user.target /etc/systemd/system/default.target
> ```

