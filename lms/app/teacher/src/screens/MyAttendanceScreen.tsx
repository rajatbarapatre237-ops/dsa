import React, { useCallback, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

type SummaryRow = {
  tid?: number | string;
  course?: string | null;
  total_days?: number | string;
  present_days?: number | string;
  absent_days?: number | string;
  attendance_percentage?: number | string;
};

type LogRow = {
  date?: string;
  course?: string | null;
  entry_time?: string | null;
  exit_time?: string | null;
  status?: string | null;
};

function formatTime(value?: string | null) {
  const text = String(value ?? '').trim();
  if (!text) return '—';
  return text.length >= 16 ? text.slice(11, 16) : text;
}

function logStatus(row: LogRow) {
  const exit = String(row.exit_time ?? '').trim();
  if (!exit) {
    return { label: 'Pending exit', tone: 'pending' as const };
  }
  const status = String(row.status ?? '').trim();
  if (status.toLowerCase() === 'present') {
    return { label: status, tone: 'present' as const };
  }
  return { label: status || '—', tone: 'default' as const };
}

function SummaryCard({ row, last }: { row: SummaryRow; last?: boolean }) {
  const percent = Number(row.attendance_percentage ?? 0);
  const percentLabel = Number.isFinite(percent) ? `${percent.toFixed(1)}%` : '—';

  return (
    <View style={[styles.summaryRow, !last && styles.rowBorder]}>
      <View style={styles.summaryMain}>
        <Text style={styles.summaryCourse}>{row.course ?? '—'}</Text>
        <Text style={styles.summaryId}>T{row.tid ?? '—'}</Text>
      </View>
      <View style={styles.summaryStats}>
        <Text style={styles.summaryMeta}>
          Total {row.total_days ?? '—'} · Present {row.present_days ?? '—'} · Absent {row.absent_days ?? '—'}
        </Text>
        <Text style={styles.summaryPercent}>{percentLabel}</Text>
      </View>
    </View>
  );
}

function LogCard({ row, last }: { row: LogRow; last?: boolean }) {
  const status = logStatus(row);

  return (
    <View
      style={[
        styles.logRow,
        !last && styles.rowBorder,
        status.tone === 'pending' && styles.logPending,
        status.tone === 'present' && styles.logPresent,
      ]}>
      <View style={styles.logMain}>
        <Text style={styles.logDate}>{row.date ?? '—'}</Text>
        <Text style={styles.logCourse}>{row.course ?? '—'}</Text>
      </View>
      <View style={styles.logTimes}>
        <Text style={styles.logTimeLabel}>
          In {formatTime(row.entry_time)} · Out {formatTime(row.exit_time)}
        </Text>
        <Text
          style={[
            styles.logStatus,
            status.tone === 'pending' && styles.statusPending,
            status.tone === 'present' && styles.statusPresent,
          ]}>
          {status.label}
        </Text>
      </View>
    </View>
  );
}

export default function MyAttendanceScreen() {
  const navigation = useNavigation<any>();
  const [loading, setLoading] = useState(true);
  const [teacherName, setTeacherName] = useState('');
  const [teacherId, setTeacherId] = useState('');
  const [summary, setSummary] = useState<SummaryRow[]>([]);
  const [log, setLog] = useState<LogRow[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.myAttendance();
      setTeacherName(res.teacher?.name ?? '');
      setTeacherId(res.teacher?.tid != null ? String(res.teacher.tid) : '');
      setSummary(res.summary ?? []);
      setLog(res.log ?? []);
    } catch {
      setSummary([]);
      setLog([]);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  const headerLabel =
    teacherName && teacherId ? `${teacherName} (T${teacherId})` : teacherName || 'Your attendance';

  return (
    <ScreenLayout
      title="My Attendance"
      subtitle={headerLabel}
      onBack={() => navigation.navigate('AttendanceHub')}
      refreshing={loading}
      onRefresh={load}>
      {loading && summary.length === 0 && log.length === 0 ? (
        <ActivityIndicator color={PRIMARY} style={{ marginTop: 24 }} />
      ) : (
        <>
          <Card title={`Summary — ${headerLabel}`}>
            <Text style={styles.hint}>
              Recorded via NFC card tap: first tap = entry, second tap after 1 hour = exit (marked Present).
            </Text>
            {summary.length > 0 ? (
              summary.map((row, index) => (
                <SummaryCard key={`${row.course}-${index}`} row={row} last={index === summary.length - 1} />
              ))
            ) : (
              <Text style={styles.empty}>
                No attendance records yet. Tap your NFC card at entry, then again after 1 hour at exit.
              </Text>
            )}
          </Card>

          <Card title="Daily log">
            {log.length > 0 ? (
              log.map((row, index) => (
                <LogCard key={`${row.date}-${row.entry_time}-${index}`} row={row} last={index === log.length - 1} />
              ))
            ) : (
              <Text style={styles.empty}>No daily records found.</Text>
            )}
          </Card>
        </>
      )}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hint: {
    fontSize: 12,
    color: theme.muted,
    marginBottom: 12,
    lineHeight: 18,
  },
  summaryRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    paddingVertical: 12,
    gap: 12,
  },
  summaryMain: {
    flex: 1,
  },
  summaryCourse: {
    fontSize: 15,
    fontWeight: '700',
    color: theme.text,
  },
  summaryId: {
    fontSize: 12,
    color: theme.muted,
    marginTop: 3,
  },
  summaryStats: {
    alignItems: 'flex-end',
    maxWidth: '52%',
  },
  summaryMeta: {
    fontSize: 11,
    color: theme.muted,
    textAlign: 'right',
  },
  summaryPercent: {
    fontSize: 18,
    fontWeight: '800',
    color: PRIMARY,
    marginTop: 4,
  },
  logRow: {
    paddingVertical: 12,
  },
  logPending: {
    backgroundColor: '#fffbeb',
    marginHorizontal: -12,
    paddingHorizontal: 12,
    borderRadius: 8,
  },
  logPresent: {
    backgroundColor: '#ecfdf3',
    marginHorizontal: -12,
    paddingHorizontal: 12,
    borderRadius: 8,
  },
  logMain: {
    marginBottom: 4,
  },
  logDate: {
    fontSize: 15,
    fontWeight: '700',
    color: theme.text,
  },
  logCourse: {
    fontSize: 12,
    color: theme.muted,
    marginTop: 2,
  },
  logTimes: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 8,
  },
  logTimeLabel: {
    flex: 1,
    fontSize: 12,
    color: theme.text,
  },
  logStatus: {
    fontSize: 12,
    fontWeight: '700',
    color: theme.text,
  },
  statusPending: {
    color: '#b45309',
  },
  statusPresent: {
    color: theme.success,
  },
  rowBorder: {
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: '#e2e8f0',
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 8,
  },
});
