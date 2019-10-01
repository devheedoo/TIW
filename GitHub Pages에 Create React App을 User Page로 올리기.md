# GitHub Pages에 Create React App을 User Page로 올리기

GitHub Pages에서 Create React App을 이용해 간단한 웹사이트를 만들었습니다. 진행과정입니다:

1. GitHub Pages 종류 확인
2. Create React App 공식 배포 문서에 따라 배포
3. Project Page 방식을 통해 문제 확인
4. 기존 계정으로 Pull Request를 통해 배포하기
5. 추가: 계정 하나만 사용해서 진행하는 방법

## 1. GitHub Pages 종류 확인

GitHub Pages는 크게 세 종류로 나뉩니다. [User, Organization, and Project Pages - GitHub Help](https://help.github.com/en/articles/user-organization-and-project-pages)의 내용을 간추려서 설명하면 다음과 같습니다:

- User Page: `<username>.github.io` 도메인을 사용하는 페이지
- Organization Page: `<orgname>.github.io` 도메인을 사용하는 페이지
- Project Page: `<username>.github.io/<projectname>` 또는 `<orgname>.github.io/<projectname>`

프로젝트명이 뒤에 붙는게 싫습니다. User Page 방식의 URL을 사용하기로 합니다. 기존 GitHub 계정은 이미 User Page를 사용중이어서 새 GitHub 계정을 생성했습니다.

## 2. Create React App 공식 배포 문서에 따라 배포

[Create React App 공식 배포 문서](https://create-react-app.dev/docs/deployment)에는 GitHub Pages에 배포하는 방법이 나와있습니다. package.json 파일을 수정하고, gh-pages 라이브러리를 추가하면 됩니다. 그리고 친절하게, project page가 아니라 user page로 사용할 경우 package.json 내용을 다르게 수정해야 한다고 알려줍니다.

하지만 공식 배포 문서를 따라 User Page 배포 과정을 진행한 후 `<username>.github.io`에 접속하면 README.md 파일만 보였습니다.

## 3. Project Page 방식을 통해 문제 확인

Project Page 방식으로 진행했더니 문제가 없었습니다.

저장소를 살펴봤더니 default branch는 `master`지만 Settings - GitHub Pages - Source 옵션은 `gh-pages` branch를 사용하고 있었습니다.

저장소를 `gh-pages` branch로 바꿔봤더니 리소스는 하나도 없고 Create React App을 빌드했을 때 build 폴더에 생성되는 코드들만 보였습니다.

Settings - GitHub Pages - Source가 가리키는 곳에 빌드 결과 파일이 위치해야 한다는 걸 알았습니다. GitHub Pages에서 웹사이트를 만들 때에는 다음과 같이 빌드 결과 파일을 올려야하는 것이었습니다.

1. `master` branch에 소스 파일 올리기: User, Organization Page
2. `gh-pages` branch에 소스 파일 올리기: Project Page
3. `master` branch의 `/docs` 폴더에 소스 파일 올리기: 특수한 경우

## 4. 기존 계정으로 Pull Request를 통해 배포하기

도메인 주소를 위해 계정을 새로 만들었지만, 코드 작업은 기존 계정에서 진행하고 싶었습니다. 그래서 Pull Request를 통해 Create React App을 GitHub Pages에 User Page로 배포하는 방법을 정리했습니다.

편의상 원래 계정을 A, 새 계정을 B라고 부르겠습니다.

1. B 계정에서 간단히 README 파일만 들어있는 `<username>.github.io` 저장소를 생성합니다.
2. A 계정에서 위 저장소를 fork하고, 코드 작업을 진행할 새 branch를 생성하고, default branch로 설정합니다.
3. 새 branch에서 Create React App 공식 배포 문서의 User Page 배포 방법을 따라 진행합니다.
4. `yarn deploy` 를 실행하면 `gh-pages -b master -d build` 옵션에 의해 빌드 결과물이 `master` branch에 생성됩니다.
5. A 계정의  `master` branch에서 B 계정의 `master` branch로 Pull Request합니다.

## 5. 추가: 계정 하나만 사용해서 진행하는 방법

[Deploy a React App as a Github User Page with Yarn - JavaScriptErika | Dev Community](https://dev.to/javascripterika/deploy-a-react-app-as-a-github-user-page-with-yarn-3fka) 글에 따르면, 저장소에서 branch를 생성해 default branch로 설정한 후 수정사항을 `master` branch에 merge하는 방식으로 가능하다고 합니다.

> 참고 자료:
>
> - [Configuring a publishing source for GitHub Pages | GitHub Help](https://help.github.com/en/articles/configuring-a-publishing-source-for-github-pages)
> - [Deployment | Create React App](https://create-react-app.dev/docs/deployment)
> - [Deploy a React App as a Github User Page with Yarn - JavaScriptErika | Dev Community](https://dev.to/javascripterika/deploy-a-react-app-as-a-github-user-page-with-yarn-3fka)


