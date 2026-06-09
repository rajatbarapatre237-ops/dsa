import React from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { LatestMarksCard, isTestPassed } from '../components/MarksUi';
import { AcademicsStackParamList } from '../navigation/types';
import { theme } from '../ui/theme';

type Props = NativeStackScreenProps<AcademicsStackParamList, 'ClassTestResultDetail'>;

export default function ClassTestResultDetailScreen({ navigation, route }: Props) {
  const { result } = route.params;
  const passed = isTestPassed(result as any);

  return (
    <ScreenLayout title="Test result" subtitle={String(result.test_name)} onBack={() => navigation.goBack()}>
      <LatestMarksCard result={result as any} />

      <Card title="Details">
        <View style={styles.detailRow}>
          <Text style={styles.label}>Subject</Text>
          <Text style={styles.value}>{String(result.subject_name ?? '—')}</Text>
        </View>
        <View style={styles.detailRow}>
          <Text style={styles.label}>Test date</Text>
          <Text style={styles.value}>{String(result.test_date ?? '—')}</Text>
        </View>
        <View style={styles.detailRow}>
          <Text style={styles.label}>Passing marks</Text>
          <Text style={styles.value}>{String(result.passing_marks ?? '—')}</Text>
        </View>
        <View style={styles.detailRow}>
          <Text style={styles.label}>Final result</Text>
          <Text style={[styles.value, passed === true && styles.pass, passed === false && styles.fail]}>
            {passed === true ? 'Pass' : passed === false ? 'Fail' : '—'}
          </Text>
        </View>
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  label: { fontSize: 13, fontWeight: '600', color: theme.muted },
  value: { fontSize: 14, fontWeight: '700', color: theme.text },
  pass: { color: theme.success },
  fail: { color: theme.danger },
});
