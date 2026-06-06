import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from 'react';
import { LmsApi } from '../api/lms';
import { AppStorage } from '../storage/AppStorage';
import {
  recordsForMonth,
  resolveCourseMeta,
  resolveStudentId,
  StoredStudentUser,
} from '../components/StudentHubUi';

type StudentContextValue = {
  loading: boolean;
  refresh: () => Promise<void>;
  applyProfile: (profile: Record<string, unknown>) => Promise<void>;
  name: string;
  studentId: string | null;
  profile: Record<string, unknown> | null;
  dashboard: Record<string, unknown> | null;
  assignments: any[];
  attendanceSummary: any[];
  monthRecords: any[];
  course: string | null;
  batch: string | null;
};

const StudentContext = createContext<StudentContextValue | null>(null);

async function syncStoredUserName(profile: Record<string, unknown> | null) {
  if (!profile?.name) return null;
  const user = await AppStorage.getUser<StoredStudentUser>();
  if (!user) return null;
  const updated = { ...user, name: String(profile.name) };
  await AppStorage.setUser(updated);
  return updated;
}

export function StudentContextProvider({ children }: { children: React.ReactNode }) {
  const [loading, setLoading] = useState(true);
  const [storedUser, setStoredUser] = useState<StoredStudentUser | null>(null);
  const [profile, setProfile] = useState<Record<string, unknown> | null>(null);
  const [dashboard, setDashboard] = useState<any>(null);
  const [assignments, setAssignments] = useState<any[]>([]);
  const [attendanceSummary, setAttendanceSummary] = useState<any[]>([]);
  const [monthRecords, setMonthRecords] = useState<any[]>([]);

  const month = useMemo(() => new Date().toISOString().slice(0, 7), []);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const user = await AppStorage.getUser<StoredStudentUser>();
      setStoredUser(user);

      const [dashResult, attendResult, assignResult, profileResult] = await Promise.allSettled([
        LmsApi.dashboard(),
        LmsApi.attendance(month),
        LmsApi.assignments(),
        LmsApi.profile(),
      ]);

      if (dashResult.status === 'fulfilled') {
        const data = dashResult.value.dashboard ?? dashResult.value;
        setDashboard(data);
        setAttendanceSummary(data?.attendance_summary ?? []);
      } else {
        setDashboard(null);
        setAttendanceSummary([]);
      }

      if (attendResult.status === 'fulfilled') {
        setMonthRecords(recordsForMonth(attendResult.value.records ?? [], month));
      } else {
        setMonthRecords([]);
      }

      if (assignResult.status === 'fulfilled') {
        setAssignments(assignResult.value.assignments ?? []);
      } else {
        setAssignments([]);
      }

      let nextProfile: Record<string, unknown> | null = null;
      if (profileResult.status === 'fulfilled') {
        nextProfile = profileResult.value.profile ?? null;
      } else if (dashResult.status === 'fulfilled') {
        const data = dashResult.value.dashboard ?? dashResult.value;
        nextProfile = data?.profile ?? null;
      }
      setProfile(nextProfile);

      const syncedUser = await syncStoredUserName(nextProfile);
      if (syncedUser) {
        setStoredUser(syncedUser);
      }
    } finally {
      setLoading(false);
    }
  }, [month]);

  const applyProfile = useCallback(async (next: Record<string, unknown>) => {
    setProfile(next);
    setDashboard((current: any) =>
      current ? { ...current, profile: { ...(current.profile ?? {}), ...next } } : current,
    );
    const syncedUser = await syncStoredUserName(next);
    if (syncedUser) {
      setStoredUser(syncedUser);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const name = String(profile?.name ?? storedUser?.name ?? 'Student');
  const studentId = resolveStudentId(profile, storedUser);
  const courseMeta = resolveCourseMeta({ profile, attendanceSummary, monthRecords });

  const value = useMemo<StudentContextValue>(
    () => ({
      loading,
      refresh: load,
      applyProfile,
      name,
      studentId,
      profile,
      dashboard,
      assignments,
      attendanceSummary,
      monthRecords,
      course: courseMeta.course,
      batch: courseMeta.batch,
    }),
    [
      loading,
      load,
      applyProfile,
      name,
      studentId,
      profile,
      dashboard,
      assignments,
      attendanceSummary,
      monthRecords,
      courseMeta.course,
      courseMeta.batch,
    ],
  );

  return React.createElement(StudentContext.Provider, { value }, children);
}

export function useStudentContext(): StudentContextValue {
  const ctx = useContext(StudentContext);
  if (!ctx) {
    throw new Error('useStudentContext must be used within StudentContextProvider');
  }
  return ctx;
}
