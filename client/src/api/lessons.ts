import api from "../lib/axios";
import type { ApiList, Lesson } from "@/types";

export const getLessons = async (facultyId: number): Promise<Lesson[]> => {
  const { data } = await api.get<ApiList<Lesson>>("/lessons", {
    params: { facultyId },
  });
  return data.data;
};
