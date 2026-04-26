import api from "@/lib/axios";
import type { ApiList, Schedule } from "@/types";

export const getScheduleByStudent = async (
  studentId: number,
): Promise<Schedule[]> => {
  const { data } = await api.get<ApiList<Schedule>>(
    `/schedules/student/${studentId}`,
  );
  return data.data;
};

export const getSchedules = async (params?: {
  facultyId?: number;
}): Promise<Schedule[]> => {
  const { data } = await api.get<ApiList<Schedule>>("/schedules", { params });
  return data.data;
};
