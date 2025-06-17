export interface Env {
	CLIENT_ID: string;
	CLIENT_SECRET: string;
	REDIRECT_URI: string;
}

export default {
	async fetch(request: Request, env: Env): Promise<Response> {
		const url = new URL(request.url);
		if (url.pathname === '/') {
			return new Response('OAuth backend running', { status: 200 });
		}
		if (url.pathname === '/oauth/callback') {
			const code = url.searchParams.get('code');
			if (!code) return new Response('Missing code', { status: 400 });
			const tokenResponse = await fetch('https://oauth2.googleapis.com/token', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: new URLSearchParams({
					code,
					client_id: env.CLIENT_ID,
					client_secret: env.CLIENT_SECRET,
					redirect_uri: env.REDIRECT_URI,
					grant_type: 'authorization_code',
				}),
			});
			if (!tokenResponse.ok) {
				const errorText = await tokenResponse.text();
				return new Response(`Token exchange failed: ${errorText}`, { status: 500 });
			}
			const tokenData = await tokenResponse.json();
			return new Response(JSON.stringify(tokenData), { status: 200, headers: { 'Content-Type': 'application/json' } });
		}
		return new Response('Not found', { status: 404 });
	},
};
