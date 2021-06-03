# moppo_server

<h1>MOPPO</h1>

모바일 프로그래밍 팀프로젝트 팀 모뽀에 사용한 php 서버 코드입니다.
DB 구성은 아래에 작성하겠습니다.
안드로이드에서 volley를 통해 php 서버와 통신하였습니다.

참고로 volley는 비동기 통신이므로 여러 예외상황이 많이 발생하므로 유기적인 연결이 필요한 프로젝트에서는 권장하지 않습니다.
작은 프로젝트임에도 잦은 오류가 발생했습니다.
어쩌면 공부가 더 됐을 수도 있습니다.
</br>
저는 이번 프로젝트를 하며 개인적으로 volley를 사용하지 않으리라 다짐했습니다.

안드로이드에서 서버와 DB를 통신할 일이 있으면 firebase를 쓰는 게 좋을 것 같습니다.

<h2>DB 구성</h2>
TABLE
</br>
1. users
  idx</br>
  userID</br>
  userPwd</br>
  name</br>
  nickname</br>
  totalMoney</br>
  updatedTime</br>
</br>
2. plans
  idx</br>
  userNo</br>
  plan_name</br>
  plan_order</br>
  income</br>
  is_complete</br>
  timestamp</br>
</br>
3. money
  idx</br>
  userNo</br>
  typeFlag //0 출금, 1 입금</br>
  typeMoney</br>
  typeNo</br>
  timestamp</br>
</br>
*plans.userNo와 money.userNo 는 users.idx와 연결된 foreign key이다.
