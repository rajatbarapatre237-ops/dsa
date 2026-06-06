import { APP_ROLE } from '../config';
import { http } from './http';

const p = `/${APP_ROLE}`;

export const LmsApi = {
  dashboard: () => http.get(`${p}/dashboard`).then(r => r.data),
  profile: () => http.get(`${p}/profile`).then(r => r.data),
  students: (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    course?: string;
    batch?: string;
  }) => http.get(`${p}/students`, { params }).then(r => r.data),
  courses: () => http.get(`${p}/courses`).then(r => r.data),
  attendance: (month?: string) =>
    http
      .get(`${p}/attendance`, { params: { month: month ?? new Date().toISOString().slice(0, 7) } })
      .then(r => r.data),
  assignments: () => http.get(`${p}/assignments`).then(r => r.data),
  assignment: (id: number) => http.get(`${p}/assignments/${id}`).then(r => r.data),
  student: (id: string) => http.get(`${p}/students/${id}`).then(r => r.data),
  allBatches: () => http.get(`${p}/form/all-batches`).then(r => r.data),
  createAssignmentLink: (body: {
    type: 'link';
    batch_name: string;
    document_name: string;
    link: string;
  }) => http.post(`${p}/assignments`, body).then(r => r.data),
  createAssignmentFile: (form: FormData) =>
    http
      .post(`${p}/assignments`, form, {
        headers: { 'Content-Type': 'multipart/form-data' },
        transformRequest: data => data,
      })
      .then(r => r.data),
  updateAssignmentStatus: (id: number, status: boolean) =>
    http.patch(`${p}/assignments/${id}/status`, { status }).then(r => r.data),
  deleteAssignment: (id: number) => http.delete(`${p}/assignments/${id}`).then(r => r.data),
  transactions: () => http.get(`${p}/transactions`).then(r => r.data),
  salary: () => http.get(`${p}/salary`).then(r => r.data),
  classTests: () => http.get(`${p}/class-tests`).then(r => r.data),
  classTestResults: () => http.get(`${p}/class-test-results`).then(r => r.data),
  allTestMarks: () => http.get(`${p}/all-test-marks`).then(r => r.data),
  testResults: () => http.get(`${p}/test-results`).then(r => r.data),
  changePassword: (body: { current_password: string; new_password: string }) =>
    http.post(`${p}/change-password`, body).then(r => r.data),

  formSessions: () => http.get(`${p}/form/sessions`).then(r => r.data),
  formCourses: () => http.get(`${p}/form/courses`).then(r => r.data),
  formBatches: (course: string) =>
    http.get(`${p}/form/batches`, { params: { course } }).then(r => r.data),
  formStudents: (params: { course: string; session: string; batch?: string }) =>
    http.get(`${p}/form/students`, { params }).then(r => r.data),
  saveAttendance: (body: {
    date: string;
    course: string;
    batch: string;
    records: { student_id: string; status: 'present' | 'absent' }[];
  }) => http.post(`${p}/form/attendance`, body).then(r => r.data),

  ctCourses: (session_name?: string) =>
    http.get(`${p}/form/class-test/courses`, { params: session_name ? { session_name } : {} }).then(r => r.data),
  ctSubjects: (course_id: number) =>
    http.get(`${p}/form/class-test/subjects`, { params: { course_id } }).then(r => r.data),
  ctTests: (course_id: number, subject_id: number) =>
    http.get(`${p}/form/class-test/tests`, { params: { course_id, subject_id } }).then(r => r.data),
  createClassTest: (body: Record<string, unknown>) =>
    http.post(`${p}/form/class-test`, body).then(r => r.data),
  ctStudentsMarks: (params: { test_id: number; session_name?: string; batch?: string }) =>
    http.get(`${p}/form/class-test/students`, { params }).then(r => r.data),
  saveClassTestMarks: (body: { test_id: number; marks: Record<string, string> }) =>
    http.post(`${p}/form/class-test/marks`, body).then(r => r.data),
};
