import api from "@/lib/axios";
import type { ApiItem, ApiList, Student, StudentFilters } from "@/types";

export const getStudents = async (
  filters?: StudentFilters,
): Promise<Student[]> => {
  const { data } = await api.get<ApiList<Student>>("/students", {
    params: filters,
  });
  return data.data;
};

export const getStudent = async (id: number): Promise<Student> => {
  const { data } = await api.get<ApiItem<Student>>(`/students/${id}`);
  return data.data;
};

export const createStudent = async (
  payload: Partial<Student>,
): Promise<Student> => {
  const { data } = await api.post<ApiItem<Student>>("/students", payload);
  return data.data;
};

export const updateStudent = async (
  id: number,
  payload: Partial<Student>,
): Promise<Student> => {
  const { data } = await api.put<ApiItem<Student>>(`/students/${id}`, payload);
  return data.data;
};

export const deleteStudent = async (id: number): Promise<void> => {
  await api.delete(`/students/${id}`);
};
