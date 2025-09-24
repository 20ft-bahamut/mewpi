# mewpi

현대적 감성으로 다시 만든 설치형 CMS.  
**Backend**: Laravel 12 (PHP 8.3+)  
**Admin/Front**: Svelte 5 (JavaScript only; TypeScript 사용 안 함)  
라이선스: MIT

- 대상: 개발사/에이전시/자사몰 운영사
- 구조: 단일 사이트(멀티사이트 지양)
- 목표: CMS 코어 + **플러그인**으로 쇼핑몰 추가/확장

## Monorepo 구조

```
api/      # Laravel 12 (백엔드 API)
admin/    # Svelte 5 관리자 SPA/SSR
front/    # Svelte 5 프론트 SPA/SSR
plugins/  # 플러그인 소스
theme/    # 테마 소스
docker/   # 도커 구성
storage/  # 업로드/캐시/로그 (api/storage와 연동)
scripts/  # 빌드/배포/마이그레이션 스크립트
docs/     # 설계/스펙 문서
tests/    # 테스트 코드
```

> **Public 경로**
> - Laravel 문서 루트: `api/public`
> - 업로드: `api/storage/app/public/**` → `api/public/storage`(symlink)로 외부 노출

## 빠른 시작 (개발 환경)

### 0) 클론 & 기본 설정
```bash
git clone https://github.com/20ft-bahamut/mewpi.git
cd mewpi
```

### 1) API (Laravel 12)
필수: PHP 8.3+, Composer, DB(MySQL/PostgreSQL), Redis(선택)
```bash
cd api
cp .env.example .env
composer install
php artisan key:generate
# DB 연결값 .env 설정 후
php artisan migrate --seed
php artisan storage:link   # /storage -> storage/app/public 심볼릭 링크
php artisan serve          # 또는 docker/nginx 사용
```

### 2) Admin (Svelte 5)
```bash
cd ../admin
npm i
npm run dev     # 개발 서버
# npm run build # 배포 번들
```

### 3) Front (Svelte 5)
```bash
cd ../front
npm i
npm run dev
# npm run build
```

### 4) 리버스 프록시(예: Nginx) 개요
- `/api/*` → Laravel (php-fpm)
- `/storage/*` → `api/storage/app/public` 정적 제공
- `/admin/*` → Admin Node 서버
- `/` → Front Node 서버

(샘플 nginx/conf는 `docker/`에 추가 예정)

## 업로드/정적 파일
- 컨텐츠/상품 이미지는 `api/storage/app/public/**`에 저장
- 외부 URL: `https://<api-host>/storage/...`
- 확장 시 S3/호환 스토리지 + CDN으로 교체 가능

## 스크립트
- 공용 스크립트는 `scripts/`에 위치 (예: `build_all.sh`, `migrate.sh`, `seed.sh`)
- CI에서는 `api` PHP 테스트 → `admin`/`front` Lint/Test/Build 순으로 수행

## 테스트
- API: Pest/PHPUnit (`api/`)
- Admin/Front: Vitest + Playwright (`admin/`, `front/`)
- 핵심 시나리오: 인증, 권한, 업로드, 캐시 무효화, 콘텐츠 CRUD

## 개발 가이드 (요약)
- API는 `/api` prefix 고정
- 인증: Admin은 Laravel Sanctum 권장
- 플러그인: `plugins/<name>` → ServiceProvider, 마이그레이션, 훅 등록
- 테마: `theme/<name>` → 레이아웃/위젯, front 빌드 파이프라인과 연동

## 라이선스
MIT © 2025 20ft-bahamut
