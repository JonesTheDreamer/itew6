export interface LoginCredentials {
  email: string;
  password: string;
}

export interface AuthUser {
  id: number;
  firstName: string;
  lastName: string;
  middleName?: string;
  email: string;
  role: string;
}

export interface AuthResponse {
  token: string;
  user: AuthUser;
}

export interface Student {
  id: number;
  studentId: string;
  userId: number;
  firstName: string;
  lastName: string;
  middleName?: string;
  email: string;
  mobileNumber?: string;
  age?: number;
  birthDate?: string;
  birthProvince?: string;
  city?: string;
  province?: string;
  programId: number;
  programName?: string;
  yearLevel: number;
  unitsTaken: number;
  unitsLeft: number;
  gpa?: number;
  status: string;
  dateEnrolled?: string;
  dateGraduated?: string;
  dateDropped?: string;
  awards?: Award[];
  extraCurriculars?: ExtraCurricular[];
  skills?: Skill[];
  grades?: Grade[];
  skillIds?: number[];
  educationalBackground?: EduBackground[];
  organizations: UserOrganization[];
}

export interface Faculty {
  id: number;
  userId: number;
  firstName: string;
  lastName: string;
  middleName?: string;
  email: string;
  mobileNumber?: string;
  age?: number;
  birthDate?: string;
  city?: string;
  province?: string;
  position?: string;
  employmentType?: string;
  employmentDate?: string;
  monthlyIncome?: number;
  department?: string;
  jobHistory?: JobHistory[];
  eduBackground?: EduBackground[];
  awards?: Award[];
  organization: UserOrganization[];
}

export interface Program {
  id: number;
  name: string;
  code: string;
  collegeId: number;
}

export interface Skill {
  id: number;
  name: string;
  isAcademic: boolean;
  students_count?: number;
}

export interface Award {
  id: number;
  userId: number;
  title: string;
  awardingDate?: string;
  awardingOrganization?: string;
  awardingLocation?: string;
}

export interface ExtraCurricular {
  activity: string;
  created_at: string;
  endDate: string;
  id: number;
  organization: string;
  role: string;
  startDate: string;
  studentId: number;
  updated_at: string;
}

export interface Grade {
  id: number;
  studentId: number;
  courseId: number;
  course?: Course;
  courseName?: string;
  term: "preliminary" | "midterm" | "finals";
  grade: number;
  academicYear: string;
}

export interface Course {
  courseCode: string;
  courseName: string;
  courseType: string;
  created_at: string;
  curriculumId: number;
  id: number;
  isRequired: boolean;
  labUnits: number | null;
  semester: number;
  units: number;
  updated_at: number;
  yearLevel: number;
}

export interface JobHistory {
  id: number;
  facultyId: number;
  position: string;
  company?: string;
  workLocation?: string;
  employmentDate?: string;
  employmentEndDate?: string;
  employmentType?: string;
}

export interface EduBackground {
  id: number;
  userId: number;
  schoolUniversity: string;
  startYear?: number;
  graduateYear?: number;
  type?: string;
  award?: string;
}

export interface ApiList<T> {
  data: T[];
  message: string;
}

export interface ApiItem<T> {
  data: T;
  message: string;
}

export interface StudentFilters {
  programId?: number;
  status?: string;
  skillId?: number;
}

export interface FacultyFilters {
  department?: string;
}

export interface ViolationNote {
  id: number;
  violationId: number;
  note: string;
  addedBy: number;
  addedByName?: string;
  created_at: string;
}

export interface Violation {
  id: number;
  studentId: number;
  title: string;
  violationDate: string;
  description?: string;
  notes?: ViolationNote[];
  created_at: string;
}

export type CreateAwardPayload = {
  studentId: number;
  title: string;
  awardingDate?: string | null;
  awardingOrganization?: string | null;
  awardingLocation?: string | null;
};

export type CreateExtraCurricularPayload = {
  studentId: number;
  activity: string;
  role?: string | null;
  organization?: string | null;
  startDate?: string | null;
  endDate?: string | null;
};

export type UserOrganization = {
  id: number;

  userId: number;
  organizationId: number;

  role: string | null;

  dateJoined: string | null; // ISO date string
  dateLeft: string | null;

  organization?: Organization; // optional eager-loaded relation
};

export type Organization = {
  id: number;

  collegeId: number;

  organizationName: string;
  organizationDescription: string | null;

  dateCreated: string | null;
  isActive: boolean;

  college?: College; // optional relation
};

export type College = {
  id: number;

  name: string;
  dean: string | null;

  dateEstablished: string | null;
  isActive: boolean;
};

export type OrgMember = {
  id: number;
  userId: number;
  organizationId: number;
  role: string | null;
  dateJoined: string | null;
  dateLeft: string | null;
  user?: {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    student?: {
      id: number;
      studentId: string;
      program?: { name: string; code?: string };
    };
    faculty?: {
      id: number;
      position?: string;
      department?: string;
    };
  };
};
