### VirtualBox CentOS Tomcat을 호스트 PC에서 연결

[https://noota.tistory.com/entry/Virtualbox-Guest%EC%97%90-%EC%84%9C%EB%B2%84-%EA%B5%AC%EC%B6%95-%ED%9B%84-Host%EB%A1%9C-%EB%84%A4%ED%8A%B8%EC%9B%8C%ED%81%AC%EC%97%90-%EC%84%9C%EB%B9%84%EC%8A%A4%ED%95%98%EA%B8%B0](https://noota.tistory.com/entry/Virtualbox-Guest에-서버-구축-후-Host로-네트워크에-서비스하기)

Settings - Network - Adaptor1 - Advanced - Port Forwarding - New

- Name: NAME
- Protocol: TCP
- Host Port: 58080 (Custom)
- Guest Port: 8080 (Tomcat Service Port on VirtualBox CentOS)

호스트 PC에서 브라우저로 localhost:58080 확인
