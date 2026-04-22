import api from "@/lib/axios";
import type { ApiItem, ApiList, Violation, ViolationNote } from "@/types";

export const getViolations = async (
  studentId: number,
): Promise<Violation[]> => {
  try {
    const { data } = await api.get<ApiList<Violation>>(
      `/students/${studentId}/violations`,
    );
    return data.data;
  } catch (error) {
    console.log(error);
    return [];
  }
};

export const createViolation = async (
  studentId: number,
  payload: { title: string; violationDate: string; description?: string },
): Promise<Violation> => {
  const { data } = await api.post<ApiItem<Violation>>(
    `/students/${studentId}/violations`,
    payload,
  );
  return data.data;
};

export const updateViolation = async (
  id: number,
  payload: { description: string },
): Promise<Violation> => {
  const { data } = await api.put<ApiItem<Violation>>(
    `/violations/${id}`,
    payload,
  );
  return data.data;
};

export const addViolationNote = async (
  violationId: number,
  payload: { note: string },
): Promise<ViolationNote> => {
  const { data } = await api.post<ApiItem<ViolationNote>>(
    `/violations/${violationId}/notes`,
    payload,
  );
  return data.data;
};
