export type RootStackParamList = {
  Login: undefined;
  Main: undefined;
};

export type AttendanceStackParamList = {
  AttendanceHub: undefined;
  TodayAttendance: undefined;
  MonthlyAttendance: undefined;
};

export type MarksStackParamList = {
  MarksHub: undefined;
  AllTestMarks: { subjectName?: string } | undefined;
  TestResults: { subjectName?: string } | undefined;
  ClassTestResultDetail: { result: Record<string, unknown> };
};

export type AccountStackParamList = {
  AccountHome: undefined;
  ChangePassword: undefined;
};
