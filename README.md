# mewpi

설치형 오픈소스 CMS 엔진  
**Backend:** Laravel 12  
**Frontend:** Svelte 5 (TypeScript 미사용)  
**DB:** MariaDB (Core), Redis (Session/Cache), MongoDB (Chat/Message)  
**License:** MIT

---

## ✨ 특징
- **설치형 솔루션**: 클라우드/온프레미스 어디든 배포 가능
- **플러그인 시스템**: 쇼핑몰, 블로그 등 기능 확장 가능
- **테마 관리**: 디자인·레이아웃·위젯 자유 구성
- **계층형 관리자 메뉴**: 플러그인 설치 시 자동 반영
- **멀티 DB 지원**: MariaDB + Redis + MongoDB

---

## 📂 폴더 구조
api/      # Laravel 12 백엔드 API 
admin/    # Svelte 5 관리자 페이지 
front/    # Svelte 5 프론트 페이지 
plugin/   # 플러그인 폴더 
theme/    # 테마 폴더 
docker/   # Docker 환경 설정 
docs/     # 문서 
tests/    # 테스트 코드 
scripts/  # 빌드/배포 스크립트

---

## 🚀 설치
```bash
git clone git@github.com:20ft-bahamut/mewpi.git
cd mewpi
# 환경변수 설정
cp .env.example .env
# 의존성 설치
composer install
npm install --prefix admin
npm install --prefix front
