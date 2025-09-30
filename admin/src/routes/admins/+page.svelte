<script>
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { api } from '../../lib/api.js';
    import { auth } from '../../lib/auth.js';



    let me = null;
    let loading = true;
    let errorMsg = '';

    async function fetchMe() {
        try {
            const token = auth.getToken();
            if (!token) {
                goto('/login');
                return;
            }
            me = await api.me(token);
        } catch (err) {
            errorMsg = err?.message || '정보를 가져오지 못했습니다.';
            // 토큰 만료/무효 시 로그인 화면으로
            if (err?.code === 'HTTP_401' || err?.code === 'ADMIN_AUTH_UNAUTHORIZED') {
                auth.logout();
                goto('/login');
            }
        } finally {
            loading = false;
        }
    }

    async function doLogout() {
        const token = auth.getToken();
        try { if (token) await api.logout(token); } catch {}
        auth.logout();
        goto('/login');
    }

    onMount(fetchMe);
</script>

<section class="section">
    <div class="container">
        <nav class="level">
            <div class="level-left">
                <h1 class="title">관리자</h1>
            </div>
            <div class="level-right">
                <button class="button is-light" on:click={doLogout}>로그아웃</button>
            </div>
        </nav>

        {#if loading}
            <progress class="progress is-small is-primary" max="100">로딩...</progress>
        {:else if errorMsg}
            <div class="notification is-danger is-light">{errorMsg}</div>
        {:else}
            <div class="box">
                <p><strong>이름</strong> {me.name}</p>
                <p><strong>이메일</strong> {me.email}</p>
                <p><strong>상태</strong> <span class="tag is-info">{me.status}</span></p>
                <p><strong>최근 로그인</strong> {me.last_login_at ?? '-'}</p>
            </div>

            <article class="message is-info">
                <div class="message-body">
                    ✅ 여기서부터 “관리자 목록/생성/상태변경” 화면을 붙이면 됩니다.
                </div>
            </article>
        {/if}
    </div>
</section>
