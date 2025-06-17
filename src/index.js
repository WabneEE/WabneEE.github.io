addEventListener("fetch", (event) => event.respondWith(handle(event.request)));

const CLIENT_ID =
  "54824982861-cc196oi7nptao8oi1oqov2qg47rcgpdv.apps.googleusercontent.com";
let users = {};

async function handle(request) {
  const url = new URL(request.url),
    path = url.pathname;

  if (path === "/auth" && request.method === "POST") {
    const { credential } = await request.json();
    const verify = await fetch(
      `https://oauth2.googleapis.com/tokeninfo?id_token=${credential}`
    );
    if (!verify.ok) return new Response(null, { status: 401 });

    const payload = await verify.json();
    if (payload.aud !== CLIENT_ID) return new Response(null, { status: 401 });

    const email = payload.email,
      name = payload.name;
    const registered = email in users;

    if (!registered) users[email] = name || email;

    return new Response(
      JSON.stringify({
        success: true,
        token: credential,
        name: users[email],
        registered,
      }),
      { headers: { "Content-Type": "application/json" } }
    );
  }

  if (path === "/delete-account" && request.method === "POST") {
    const auth = request.headers.get("Authorization") || "";
    const token = auth.replace("Bearer ", "");
    try {
      const payload = JSON.parse(atob(token.split(".")[1]));
      delete users[payload.email];
      return new Response(JSON.stringify({ success: true }), {
        headers: { "Content-Type": "application/json" },
      });
    } catch {
      return new Response(null, { status: 401 });
    }
  }

  return new Response("Not found", { status: 404 });
}
