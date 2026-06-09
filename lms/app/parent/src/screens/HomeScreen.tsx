import React, { useCallback, useState } from 'react';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard } from '../components/Card';
import { AttendanceOverviewCard, HeroCard, SectionTitle } from '../components/DashboardUi';
import { formatStudentDisplayId } from '../utils/studentId';
import { LmsApi } from '../api/lms';
import { APP_SUBTITLE } from '../config';
import { theme } from '../ui/theme';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

export default function HomeScreen() {
  const navigation = useNavigation<any>();
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [data, setData] = useState<any>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.dashboard();
        setData(res.dashboard ?? res);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

  const child = data?.child;
  const att = data?.today_attendance;

  return (
    <ScreenLayout
      title="Dashboard"
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <HeroCard
        eyebrow={APP_SUBTITLE}
        title={child?.name ?? 'Your child'}
        subtitle="Stay updated on attendance and academic progress"
        avatarLabel={child?.name}
        chips={[
          { label: formatStudentDisplayId(child?.id) ?? 'ID —', icon: 'card-outline' },
          { label: child?.course_name ?? 'Course —', icon: 'school-outline' },
        ]}
      />

      <AttendanceOverviewCard
        date={att?.date}
        status={att?.status}
        entry={att?.entry_time}
        exit={att?.exit_time}
      />

      <SectionTitle>Quick access</SectionTitle>
      <ActionCard
        iconName="calendar-check"
        title="Attendance"
        subtitle="Today's status and monthly history"
        accent="#e0f2fe"
        onPress={() => navigation.navigate('Attendance')}
      />
      <ActionCard
        iconName="chart-box-outline"
        title="Marks & results"
        subtitle="Class tests and scored results"
        accent="#ede9fe"
        onPress={() => navigation.navigate('Marks')}
      />
      <ActionCard
        iconName="account-circle-outline"
        title="Account"
        subtitle="Profile and security settings"
        accent={theme.primarySoft}
        onPress={() => navigation.navigate('Account')}
      />
    </ScreenLayout>
  );
}
