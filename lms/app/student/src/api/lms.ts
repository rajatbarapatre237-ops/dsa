import { APP_ROLE } from '../config';
import { http } from './http';

const p = `/${APP_ROLE}`;

export const LmsApi = {
  dashboard: () => http.get(`${p}/dashboard`).then(r => r.data),
  profile: () => http.get(`${p}/profile`).then(r => r.data),
  updateProfile: (body: Record<string, string>) =>
    http.post(`${p}/profile`, body).then(r => r.data),
  students: () => http.get(`${p}/students`).then(r => r.data),
  courses: () => http.get(`${p}/courses`).then(r => r.data),
  attendance: (month?: string) =>
    http.get(`${p}/attendance`, { params: month ? { month } : {} }).then(r => r.data),
  assignments: (params?: {
    content_kind?: 'assignment' | 'note';
    subject_id?: number;
  }) => http.get(`${p}/assignments`, { params }).then(r => r.data),
  contentSubjects: (content_kind: 'assignment' | 'note') =>
    http.get(`${p}/content-subjects`, { params: { content_kind } }).then(r => r.data),
  assignment: (id: number) => http.get(`${p}/assignments/${id}`).then(r => r.data),
  transactions: () => http.get(`${p}/transactions`).then(r => r.data),
  salary: () => http.get(`${p}/salary`).then(r => r.data),
  classTests: () => http.get(`${p}/class-tests`).then(r => r.data),
  classTestResults: () => http.get(`${p}/class-test-results`).then(r => r.data),
  allTestMarks: () => http.get(`${p}/all-test-marks`).then(r => r.data),
  testResults: () => http.get(`${p}/test-results`).then(r => r.data),
  changePassword: (body: { current_password: string; new_password: string }) =>
    http.post(`${p}/change-password`, body).then(r => r.data),
};
