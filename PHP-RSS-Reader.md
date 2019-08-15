# PHP RSS Reader

RSS 피드 목록을 읽고 RSS 데이터를 가져와서 데이터베이스에 저장한다.

- CentOS 6
- [SimplePie](https://github.com/simplepie/simplepie) - A simple Atom/RSS parsing library for PHP
- [MySQL](https://www.mysql.com/) - RDBMS
- cron - Job Scheduler for Linux

> RHEL 6와 CentOS 6에서는 anacron을 사용한다. 
>
> 참고: [WEBDIR :: [CentOS] Anacron](https://webdir.tistory.com/175)

## RSS 파싱

### 1. SimplePie 설치

1. [SimplePie: Downloads](https://simplepie.org/downloads/) 에서 라이브러리를 다운로드 받는다.
2. PHP 프로젝트의 루트 경로에 `php`, `cache` 폴더를 생성하고, 777 권한을 부여한다.
3. 다운로드 받은 파일 안에 있는 `library` 폴더와 `autouploader.php` 파일을 `php` 폴더에 넣는다.
4. SimplePie를 사용할 준비가 됐다.

> 참고: [SimplePie Documentation: Setup and Getting Started](https://simplepie.org/wiki/setup/setup)

### 2. RSS 피드를 사용해 데이터 조회 및 파싱

#### RSS 피드 만들기

- 네이버: [뉴스] 카테고리에서 검색하면 우측에 [뉴스검색 RSS 보기] 버튼이 있다. 클릭하면 상세검색 옵션이 포함된 네이버 뉴스 검색 RSS 피드 주소를 얻을 수 있다. *[RSS 2.0](https://validator.w3.org/feed/docs/rss2.html)을 사용한다.*
- 구글: [뉴스] 카테고리에서 검색 후 하단의 [알림 만들기] - [옵션 표시] - [수신 위치] - [RSS 피드] 선택해서 알림을 만든다. RSS 모양 아이콘을 클릭하면 RSS 피드 주소를 얻을 수 있다. *[Atom](https://validator.w3.org/feed/docs/atom.html)을 사용한다.*

#### SimplePie 초기 설정

```php
<?php

require_once('../../php/autoloader.php');

$feed = new SimplePie();

// 2개 이상의 RSS 피드 조회 (결과는 합쳐져서 정렬된다.)
$feed->set_feed_url(array(
    'http://newssearch.naver.com/search.naver?where=rss&query=%EC%A4%91%EC%86%8C%ED%98%95%20%EC%9B%90%EC%9E%90%EB%A1%9C&field=0&nx_search_query=&nx_and_query=&nx_sub_query=&nx_search_hlquery=&is_dts=0',
    'http://digg.com/rss/index.xml',
    'http://newssearch.naver.com/search.naver?where=rss&query=%EC%9B%90%EC%9E%90%EB%A0%A5%20%EC%9C%A0%EB%9F%BD&field=0&nx_search_query=&nx_and_query=&nx_sub_query=&nx_search_hlquery=&is_dts=0',
));

// 피드 객체 생성
$feed->init();

// This will work if all of the feeds accept the same settings.
$feed->handle_content_type();

// Set cache
$feed->set_cache_location('../cache');

foreach ($feed->get_items() as $item) {

		echo $item->get_title();
		echo $item->get_date();
		echo $item->get_description();

    // <author> 태그 값이 제대로 출력되지 않아 커스텀 코드 추가
    // e.g.. <author>한국신문</author> 값이 빈 칸으로 나온다.
    $author = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'author');
    if ($author) {
        $author_value = $author[0]['data'];
        echo $author_value . "<br/>";
    } else {
        $author_value = '';
    }
    
    // htmlspecialchars('STRING', ENT_QUOTES) : 문자열에서 작은따옴표, 큰따옴표만 HTML ESCAPE 처리
    // html_entity_decode('STRING', ENT_QUOTES) : 복구
    $rss_news_insert_sql .= "(1, '"
        . htmlspecialchars($item->get_title(), ENT_QUOTES) . "', '"
        . $item->get_link() . "', '"
        . htmlspecialchars($item->get_description(), ENT_QUOTES) . "', '"
        . $author_value . "', '"
        . $item->get_date('Y-m-d H-i-s') . "', NOW()), ";
}

```

- 서로 다른 RSS 기준을 사용하더라도 모두 잘 읽어온다.
- 여러 피드에서 RSS 데이터를 가져오더라도 모두 합쳐져서 날짜 최신순으로 정렬된다.
- RSS 2.0, Atom 종류에 상관없이 데이터를 한 번에 함께 잘 가져온다.

> 참고1: [SimplePie Documentation: Tips, Tricks, Tutorials, and Screencasts](https://simplepie.org/wiki/tutorial/start)
> 
> 참고2: [Parsing Custom node value through SimplePie | Stack Overflow](https://stackoverflow.com/questions/15779631/parsing-custom-node-value-through-simplepie)
