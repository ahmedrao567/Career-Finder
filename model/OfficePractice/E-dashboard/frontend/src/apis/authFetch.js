import { getCookie, setCookie, deleteCookie } from "../Utils/cookies";
export async function authFetch(url, options = {}) {
  const accessToken = getCookie("accessToken");
  let res = await fetch(url, {
    ...options,
    headers: {
      ...options.headers,
      Authorization: `Bearer ${accessToken}`,
    },
  });
  if (res.status === 401) {
    try {
      const refreshToken = getCookie("refreshToken");
      const refreshRes = await fetch(
        "http://localhost:3000/api/token",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ refreshToken }),
        }
      );
      if (!refreshRes.ok) throw new Error("Refresh failed");
      const data = await refreshRes.json();
      setCookie("accessToken", data.token, 1);

     
      res = await fetch(url, {
        ...options,
        headers: {
          ...options.headers,
          Authorization: `Bearer ${data.token}`,
        },
      });
    } catch (err) {
      deleteCookie("accessToken");
      deleteCookie("refreshToken");
      window.location.href = "/login";
      throw err;
    }
  }
  return res;
}
