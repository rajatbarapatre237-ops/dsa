import React from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import { WorkStackParamList } from '../navigation/types';
import { formatStudentDisplayId } from '../utils/student';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';

type Props = NativeStackScreenProps<WorkStackParamList, 'ClassTestResultDetail'>;

function resultStatus(result: Record<string, unknown>): 'pass' | 'fail' | 'unknown' {
  const marks = result.marks_obtained;
  const passing = result.passing_marks;
  if (marks == null || marks === '' || passing == null || passing === '') {
    return 'unknown';
  }
  return Number(marks) >= Number(passing) ? 'pass' : 'fail';
}

export default function ClassTestResultDetailScreen({ navigation, route }: Props) {
  const { result } = route.params;
  const status = resultStatus(result);
  const marksObtained =
    result.marks_obtained != null && result.marks_obtained !== '' ? Number(result.marks_obtained) : null;
  const totalMarks =
    result.total_marks != null && result.total_marks !== '' ? Number(result.total_marks) : null;
  const passingMarks =
    result.passing_marks != null && result.passing_marks !== '' ? Number(result.passing_marks) : null;
  const percentage =
    marksObtained != null && totalMarks != null && totalMarks > 0
      ? Math.round((marksObtained / totalMarks) * 100)
      : null;
  const progress =
    marksObtained != null && totalMarks != null && totalMarks > 0
      ? Math.min(100, (marksObtained / totalMarks) * 100)
      : 0;
  const studentName = String(result.student_name ?? '').trim() || `Student ${result.student_id ?? '—'}`;

  return (
    <ScreenLayout title="Test result" subtitle={String(result.test_name ?? '')} onBack={() => navigation.goBack()}>
      <Card>
        <View style={styles.hero}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>{studentName.charAt(0).toUpperCase()}</Text>
          </View>
          <View style={styles.heroText}>
            <Text style={styles.studentName}>{studentName}</Text>
            <Text style={styles.studentId}>{formatStudentDisplayId(result.student_id) ?? '—'}</Text>
          </View>
          <View
            style={[
              styles.badge,
              status === 'pass' ? styles.badgePass : status === 'fail' ? styles.badgeFail : styles.badgeUnknown,
            ]}>
            <Text
              style={[
                styles.badgeText,
                status === 'pass'
                  ? styles.badgeTextPass
                  : status === 'fail'
                    ? styles.badgeTextFail
                    : styles.badgeTextUnknown,
              ]}>
              {status === 'pass' ? 'PASS' : status === 'fail' ? 'FAIL' : 'N/A'}
            </Text>
          </View>
        </View>

        <View style={styles.scoreCard}>
          <View style={styles.scoreMain}>
            <Text style={styles.scoreValue}>
              {marksObtained != null ? marksObtained.toFixed(0) : '—'}
              <Text style={styles.scoreTotal}>
                {totalMarks != null ? ` / ${totalMarks.toFixed(0)}` : ''}
              </Text>
            </Text>
            <Text style={styles.scoreLabel}>Marks obtained</Text>
          </View>
          {percentage != null ? (
            <View style={styles.percentBox}>
              <Text style={styles.percentValue}>{percentage}%</Text>
              <Text style={styles.percentLabel}>Score</Text>
            </View>
          ) : null}
        </View>

        <View style={styles.progressTrack}>
          <View style={[styles.progressFill, { width: `${progress}%` }]} />
        </View>

        <View style={styles.metaGrid}>
          <View style={styles.metaItem}>
            <Text style={styles.metaLabel}>Test</Text>
            <Text style={styles.metaValue}>{result.test_name ?? '—'}</Text>
          </View>
          <View style={styles.metaItem}>
            <Text style={styles.metaLabel}>Date</Text>
            <Text style={styles.metaValue}>{result.test_date ?? '—'}</Text>
          </View>
          <View style={styles.metaItem}>
            <Text style={styles.metaLabel}>Subject</Text>
            <Text style={styles.metaValue}>{result.subject_name ?? '—'}</Text>
          </View>
          <View style={styles.metaItem}>
            <Text style={styles.metaLabel}>Course</Text>
            <Text style={styles.metaValue}>{result.course_name ?? '—'}</Text>
          </View>
        </View>

        <View style={styles.divider} />

        <DetailRow
          label="Passing marks"
          value={passingMarks != null ? passingMarks.toFixed(0) : 'Not set'}
        />
        {result.remarks ? <DetailRow label="Remarks" value={result.remarks} /> : null}
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hero: { flexDirection: 'row', alignItems: 'center', gap: 12, marginBottom: 16 },
  avatar: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: '#e8f2fb',
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: { fontSize: 18, fontWeight: '800', color: PRIMARY },
  heroText: { flex: 1 },
  studentName: { fontSize: 17, fontWeight: '800', color: theme.text },
  studentId: { fontSize: 12, color: theme.muted, marginTop: 2, fontWeight: '600' },
  badge: { paddingHorizontal: 10, paddingVertical: 6, borderRadius: 999 },
  badgePass: { backgroundColor: '#ecfdf3' },
  badgeFail: { backgroundColor: '#fef2f2' },
  badgeUnknown: { backgroundColor: '#f1f5f9' },
  badgeText: { fontSize: 12, fontWeight: '800', letterSpacing: 0.3 },
  badgeTextPass: { color: theme.success },
  badgeTextFail: { color: theme.danger },
  badgeTextUnknown: { color: theme.muted },
  scoreCard: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#f8fafc',
    borderRadius: 14,
    padding: 16,
    marginBottom: 10,
  },
  scoreMain: { flex: 1 },
  scoreValue: { fontSize: 32, fontWeight: '800', color: theme.text },
  scoreTotal: { fontSize: 18, fontWeight: '600', color: theme.muted },
  scoreLabel: { fontSize: 12, color: theme.muted, marginTop: 4, fontWeight: '600' },
  percentBox: { alignItems: 'flex-end' },
  percentValue: { fontSize: 24, fontWeight: '800', color: PRIMARY },
  percentLabel: { fontSize: 12, color: theme.muted, marginTop: 2, fontWeight: '600' },
  progressTrack: {
    height: 8,
    backgroundColor: '#e2e8f0',
    borderRadius: 999,
    overflow: 'hidden',
    marginBottom: 16,
  },
  progressFill: { height: '100%', backgroundColor: PRIMARY, borderRadius: 999 },
  metaGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
  },
  metaItem: {
    width: '48%',
    backgroundColor: '#fff',
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: '#e2e8f0',
    borderRadius: 12,
    padding: 12,
  },
  metaLabel: { fontSize: 11, fontWeight: '700', color: theme.muted, textTransform: 'uppercase' },
  metaValue: { fontSize: 14, fontWeight: '600', color: theme.text, marginTop: 4 },
  divider: { height: StyleSheet.hairlineWidth, backgroundColor: '#e2e8f0', marginVertical: 14 },
});
