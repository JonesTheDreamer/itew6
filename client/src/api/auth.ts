import api from "@/lib/axios";
import type { ApiItem, AuthResponse, LoginCredentials } from "@/types";

export const login = async (
  credentials: LoginCredentials,
): Promise<AuthResponse> => {
  const { data } = await api.post<ApiItem<AuthResponse>>("/login", credentials);
  return data.data;
};

export const logout = async (): Promise<void> => {
  await api.post("/logout");
  localStorage.removeItem("auth_token");
};
