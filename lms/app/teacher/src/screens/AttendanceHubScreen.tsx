import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { ActionCard, Card } from '../components/Card';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

type AttendanceRecord = {
  date: string;
  sid: string | number;
  status: string;
  course?: string;
  batch?: string;
  student_name?: string;
};

function isPresent(status: string) {
  return (status ?? '').trim().toLowerCase() === 'present';
}

function localISODate(date: Date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

function formatRecordLine(record: AttendanceRecord) {
  const status = isPresent(record.status) ? 'Present' : 'Absent';
  const name = record.student_name?.trim() || `SID ${record.sid}`;
  return `${record.date} · ${name} · ${status}`;
}

export default function AttendanceHubScreen() {
  const navigation = useNavigation<any>();
  const { refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [records, setRecords] = useState<AttendanceRecord[]>([]);
  const [todayStats, setTodayStats] = useState<{
    total: number;
    present: number;
    absent: number;
  } | null>(null);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.attendance();
        setRecords(res.records ?? []);
        setTodayStats({
          total: Number(res.today_total_students ?? 0),
          present: Number(res.today_present_students ?? 0),
          absent: Number(res.today_absent_students ?? 0),
        });
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

  const today = localISODate(new Date());
  const todayRecords = records.filter(r => String(r.date).slice(0, 10) === today);
  const computedPresentCount = todayRecords.filter(r => isPresent(r.status)).length;
  const presentCount = todayStats?.present ?? computedPresentCount;
  const totalFallback = todayRecords.length;
  const totalStudents = todayStats?.total ?? totalFallback;
  const absentCount = todayStats?.absent ?? Math.max(0, totalStudents - presentCount);
  const dayLabel = new Date().toLocaleString('en-IN', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
  const recentRecords = (todayRecords.length ? todayRecords : records).slice(0, 4);

  return (
    <ScreenLayout
      title="Attendance"
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <Card>
        <Text style={styles.monthLabel}>{dayLabel}</Text>
        <Text style={styles.summaryHint}>Today's attendance summary</Text>
        <View style={styles.statsRow}>
          <View style={[styles.statBox, styles.statPresent]}>
            <Text style={[styles.statValue, styles.statPresentText]}>{presentCount}</Text>
            <Text style={styles.statLabel}>Present</Text>
          </View>
          <View style={[styles.statBox, styles.statAbsent]}>
            <Text style={[styles.statValue, styles.statAbsentText]}>{absentCount}</Text>
            <Text style={styles.statLabel}>Absent</Text>
          </View>
          <View style={styles.statBox}>
            <Text style={styles.statValue}>{totalStudents}</Text>
            <Text style={styles.statLabel}>Total students</Text>
          </View>
        </View>
      </Card>

      <Text style={styles.section}>Actions</Text>
      <ActionCard
        iconName="account-check-outline"
        title="Mark attendance"
        subtitle="Select course, batch, and mark students for today"
        onPress={() => navigation.navigate('AddAttendance')}
      />
      <ActionCard
        iconName="calendar-outline"
        title="View attendance"
        subtitle="Daily student records and per-student summaries"
        onPress={() => navigation.navigate('ViewAttendance')}
      />
      <ActionCard
        iconName="clipboard-list-outline"
        title="My attendance"
        subtitle="Your NFC entry/exit log and summary by course"
        onPress={() => navigation.navigate('MyAttendance')}
      />

      <Text style={styles.section}>Recent activity</Text>
      <Card>
        {recentRecords.length > 0 ? (
          recentRecords.map((record, index) => (
            <View
              key={`${record.date}-${record.sid}-${index}`}
              style={[styles.recentRow, index > 0 && styles.recentRowBorder]}>
              <View
                style={[
                  styles.statusDot,
                  isPresent(record.status) ? styles.statusPresent : styles.statusAbsent,
                ]}
              />
              <Text style={styles.recentText}>{formatRecordLine(record)}</Text>
            </View>
          ))
        ) : todayStats ? (
          <Text style={styles.empty}>No attendance marked this month yet</Text>
        ) : null}
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  monthLabel: {
    fontSize: 18,
    fontWeight: '700',
    color: theme.text,
  },
  summaryHint: {
    fontSize: 13,
    color: theme.muted,
    marginTop: 4,
    marginBottom: 14,
  },
  section: {
    fontSize: 13,
    fontWeight: '700',
    color: theme.muted,
    marginBottom: 10,
    marginTop: 4,
    textTransform: 'uppercase',
    letterSpacing: 0.4,
  },
  statsRow: {
    flexDirection: 'row',
    gap: 10,
  },
  statBox: {
    flex: 1,
    backgroundColor: '#f8fafc',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
  },
  statPresent: { backgroundColor: '#ecfdf3' },
  statAbsent: { backgroundColor: '#fef2f2' },
  statValue: {
    fontSize: 22,
    fontWeight: '800',
    color: theme.text,
  },
  statPresentText: { color: theme.success },
  statAbsentText: { color: theme.danger },
  statLabel: {
    fontSize: 11,
    color: theme.muted,
    marginTop: 4,
    fontWeight: '600',
  },
  recentRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 10,
  },
  recentRowBorder: {
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: '#e2e8f0',
  },
  statusDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginRight: 10,
  },
  statusPresent: { backgroundColor: theme.success },
  statusAbsent: { backgroundColor: theme.danger },
  recentText: {
    flex: 1,
    fontSize: 13,
    color: theme.text,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
});
