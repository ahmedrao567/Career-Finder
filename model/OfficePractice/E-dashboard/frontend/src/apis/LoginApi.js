
import { setCookie } from "../Utils/cookies";
import { authFetch } from "./authfetch";

export const response1 = async (data) => {
  try {
    const response = await authFetch ("http://localhost:3000/api/auth/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    const resData = await response.json();
    return resData;

  } catch (err) {
    console.error(err);
  }
};
