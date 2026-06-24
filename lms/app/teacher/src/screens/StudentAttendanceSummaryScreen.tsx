import React, { useCallback, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { AttendanceStackParamList } from '../navigation/types';
import { theme } from '../ui/theme';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';
import { useStaleLoad } from '../hooks/useStaleLoad';

type SummaryRow = {
  sid?: string | number;
  course?: string;
  batch?: string;
  total_days?: number | string;
  present_days?: number | string;
  absent_days?: number | string;
  attendance_percentage?: number | string;
};

type Props = NativeStackScreenProps<AttendanceStackParamList, 'StudentAttendanceSummary'>;

function SummaryTableRow({ row, last }: { row: SummaryRow; last?: boolean }) {
  const percent = Number(row.attendance_percentage ?? 0);
  const percentLabel = Number.isFinite(percent) ? `${percent.toFixed(1)}%` : '—';

  return (
    <View style={[styles.tableRow, !last && styles.tableRowBorder]}>
      <View style={styles.tableMain}>
        <Text style={styles.tableCourse}>{row.course ?? '—'}</Text>
        <Text style={styles.tableBatch}>{formatStudentBatch(row.batch) ?? '—'}</Text>
      </View>
      <View style={styles.tableStats}>
        <Text style={styles.statLine}>
          Total <Text style={styles.statValue}>{row.total_days ?? '—'}</Text>
        </Text>
        <Text style={styles.statLine}>
          Present <Text style={[styles.statValue, styles.presentText]}>{row.present_days ?? '—'}</Text>
        </Text>
        <Text style={styles.statLine}>
          Absent <Text style={[styles.statValue, styles.absentText]}>{row.absent_days ?? '—'}</Text>
        </Text>
        <Text style={styles.percent}>{percentLabel}</Text>
      </View>
    </View>
  );
}

export default function StudentAttendanceSummaryScreen({ navigation, route }: Props) {
  const { id, name } = route.params;
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [summary, setSummary] = useState<SummaryRow[]>([]);
  const [studentName, setStudentName] = useState(name ?? '');

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.studentAttendanceSummary(id);
        setSummary(res.summary ?? []);
        setStudentName(res.student?.name ?? name ?? '');
        markHasData();
      } catch {
        setSummary([]);
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, id, markHasData, name],
  );

  useRefreshOnFocus(load);

  const displayId = formatStudentDisplayId(id) ?? id;

  return (
    <ScreenLayout
      title="Attendance summary"
      subtitle={`${displayId}${studentName ? ` · ${studentName}` : ''}`}
      onBack={() => navigation.goBack()}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      {loading && summary.length === 0 ? (
        <ActivityIndicator color={PRIMARY} style={{ marginTop: 24 }} />
      ) : (
        <Card title="By course and batch">
          {summary.length > 0 ? (
            summary.map((row, index) => (
              <SummaryTableRow key={`${row.course}-${row.batch}-${index}`} row={row} last={index === summary.length - 1} />
            ))
          ) : (
            <Text style={styles.empty}>No attendance records for this student yet.</Text>
          )}
        </Card>
      )}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  tableRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    paddingVertical: 12,
    gap: 12,
  },
  tableRowBorder: {
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: '#e2e8f0',
  },
  tableMain: {
    flex: 1,
  },
  tableCourse: {
    fontSize: 15,
    fontWeight: '700',
    color: theme.text,
  },
  tableBatch: {
    fontSize: 12,
    color: theme.muted,
    marginTop: 3,
  },
  tableStats: {
    alignItems: 'flex-end',
    minWidth: 110,
  },
  statLine: {
    fontSize: 11,
    color: theme.muted,
    marginBottom: 2,
  },
  statValue: {
    fontWeight: '700',
    color: theme.text,
  },
  presentText: {
    color: theme.success,
  },
  absentText: {
    color: theme.danger,
  },
  percent: {
    fontSize: 18,
    fontWeight: '800',
    color: PRIMARY,
    marginTop: 4,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
});
