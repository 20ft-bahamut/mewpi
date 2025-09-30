<script>
	import favicon from '$lib/assets/favicon.svg';

	let { children } = $props();
    // 전역 스타일은 루트 레이아웃에서 import 해야 전체 라우트에 적용됩니다.
    import '../styles/main.scss';
</script>

<svelte:head>
	<link rel="icon" href={favicon} />
</svelte:head>

{@render children?.()}
