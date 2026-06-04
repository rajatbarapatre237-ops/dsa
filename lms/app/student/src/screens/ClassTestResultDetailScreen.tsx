import React from 'react';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import { AssignmentsStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<AssignmentsStackParamList, 'ClassTestResultDetail'>;

export default function ClassTestResultDetailScreen({ navigation, route }: Props) {
  const { result } = route.params;
  const passed =
    result.marks_obtained != null &&
    result.passing_marks != null &&
    Number(result.marks_obtained) >= Number(result.passing_marks);

  return (
    <ScreenLayout title="Test result" subtitle={String(result.test_name)} onBack={() => navigation.goBack()}>
      <Card>
        <DetailRow label="Test" value={String(result.test_name)} />
        <DetailRow label="Date" value={String(result.test_date)} />
        <DetailRow label="Subject" value={String(result.subject_name ?? '')} />
        <DetailRow label="Marks" value={`${result.marks_obtained} / ${result.total_marks}`} />
        <DetailRow label="Passing marks" value={String(result.passing_marks)} />
        <DetailRow label="Result" value={passed ? 'Pass' : 'Fail'} />
      </Card>
    </ScreenLayout>
  );
}
