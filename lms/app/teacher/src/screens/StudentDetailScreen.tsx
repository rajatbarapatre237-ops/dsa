import React, { useCallback, useState } from 'react';
import { Alert, ActivityIndicator } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import {
  AttendanceRecordCard,
  EmptyAttendanceState,
  ProfileDetailsCard,
  ProfileInfoRow,
  StudentProfileHero,
  StudentProfileSectionTitle,
  StudentQuickStats,
} from '../components/StudentProfileUi';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { StudentsStackParamList } from '../navigation/types';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { formatStudentBatch, formatStudentDisplayId } from '../utils/student';

type Props = NativeStackScreenProps<StudentsStackParamList, 'StudentDetail'>;

export default function StudentDetailScreen({ navigation, route }: Props) {
  const { id } = route.params;
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [data, setData] = useState<any>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        setData(await LmsApi.student(id));
        markHasData();
      } catch (e: any) {
        Alert.alert('Error', e?.message ?? 'Could not load student');
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, id, markHasData],
  );

  useRefreshOnFocus(load);

  const s = data?.student;
  const batch = formatStudentBatch(s?.batch);
  const recentAttendance = data?.recent_attendance ?? [];
  const chips = [
    s?.course_name ? { label: s.course_name, icon: 'school-outline' } : null,
    batch ? { label: batch, icon: 'people-outline' } : null,
    s?.school_name && s.school_name !== 'No name'
      ? { label: s.school_name, icon: 'business-outline' }
      : null,
  ].filter(Boolean) as { label: string; icon: string }[];

  return (
    <ScreenLayout
      title="Student profile"
      onBack={() => navigation.goBack()}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      {loading && !s ? <ActivityIndicator color={PRIMARY} style={{ marginTop: 24 }} /> : null}
      {s ? (
        <>
          <StudentProfileHero
            name={s.name}
            studentId={formatStudentDisplayId(s.id)}
            avatarLabel={s.name}
            chips={chips}
          />

          <StudentQuickStats
            studentId={formatStudentDisplayId(s.id)}
            feesBalance={s.balance_fees}
            schoolName={s.school_name}
          />

          <StudentProfileSectionTitle>Contact</StudentProfileSectionTitle>
          <ProfileDetailsCard title="Reach student">
            <ProfileInfoRow icon="call-outline" label="Mobile" value={s.mobile} />
            <ProfileInfoRow icon="mail-outline" label="Email" value={s.email} last />
          </ProfileDetailsCard>

          <StudentProfileSectionTitle>Academics</StudentProfileSectionTitle>
          <ProfileDetailsCard title="Enrollment">
            <ProfileInfoRow icon="school-outline" label="Course" value={s.course_name} />
            <ProfileInfoRow icon="people-outline" label="Batch" value={batch ?? 'Not set'} />
            <ProfileInfoRow icon="business-outline" label="School" value={s.school_name} last />
          </ProfileDetailsCard>

          <StudentProfileSectionTitle>Recent attendance</StudentProfileSectionTitle>
          <ProfileDetailsCard title="Last records">
            {recentAttendance.length > 0 ? (
              recentAttendance.map((r: any, index: number) => (
                <AttendanceRecordCard
                  key={`${r.date}-${r.sid}`}
                  date={r.date}
                  status={r.status}
                  course={r.course}
                  batch={r.batch}
                  last={index === recentAttendance.length - 1}
                />
              ))
            ) : (
              <EmptyAttendanceState />
            )}
          </ProfileDetailsCard>
        </>
      ) : null}
    </ScreenLayout>
  );
}
