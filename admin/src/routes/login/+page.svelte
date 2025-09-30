<script>
    import { api } from '../../lib/api.js';
    import { auth } from '../../lib/auth.js';
    import { goto } from '$app/navigation';

    let email = '';
    let password = '';
    let errorMsg = '';
    let loading = false;

    async function submit(e) {
        e.preventDefault();
        errorMsg = '';
        loading = true;
        try {
            const res = await api.loginAdmin({ email, password });
            auth.setToken(res.token);
            goto('/admins');
        } catch (err) {
            errorMsg = err?.message || '로그인 실패';
        } finally {
            loading = false;
        }
    }
</script>

<section class="section">
    <div class="container" style="max-width:480px;">
        <h1 class="title has-text-centered">mewpi admin</h1>
        <form on:submit|preventDefault={submit} class="box">
            {#if errorMsg}
                <div class="notification is-danger is-light">{errorMsg}</div>
            {/if}

            <div class="field">
                <label class="label">이메일</label>
                <div class="control">
                    <input class="input" type="email" bind:value={email} required />
                </div>
            </div>

            <div class="field">
                <label class="label">비밀번호</label>
                <div class="control">
                    <input class="input" type="password" bind:value={password} required />
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary is-fullwidth" disabled={loading}>
                        {#if loading}로그인 중...{:else}로그인{/if}
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
