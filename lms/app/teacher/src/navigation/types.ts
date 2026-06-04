export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
};

export type WorkStackParamList = {
  WorkHub: undefined;
  AssignmentsList: undefined;
  AddAssignment: undefined;
  AssignmentDetail: { id: number };
  AssignmentFile: { id: number };
  ClassTests: undefined;
  CreateClassTest: undefined;
  EnterMarks: undefined;
  TestResults: undefined;
  ClassTestResultDetail: { result: Record<string, unknown> };
};

export type StudentsStackParamList = {
  StudentsList: undefined;
  StudentDetail: { id: string };
};

export type AttendanceStackParamList = {
  AttendanceHub: undefined;
  ViewAttendance: undefined;
  AddAttendance: undefined;
};
