import React from 'react';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import { WorkStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<WorkStackParamList, 'ClassTestResultDetail'>;

export default function ClassTestResultDetailScreen({ navigation, route }: Props) {
  const { result } = route.params;
  const passed =
    result.marks_obtained != null &&
    result.passing_marks != null &&
    Number(result.marks_obtained) >= Number(result.passing_marks);

  return (
    <ScreenLayout title="Test result" subtitle={result.test_name} onBack={() => navigation.goBack()}>
      <Card>
        <DetailRow label="Test" value={result.test_name} />
        <DetailRow label="Date" value={result.test_date} />
        <DetailRow label="Subject" value={result.subject_name} />
        <DetailRow label="Student ID" value={result.student_id} />
        <DetailRow label="Marks" value={`${result.marks_obtained} / ${result.total_marks}`} />
        <DetailRow label="Passing marks" value={result.passing_marks} />
        <DetailRow label="Result" value={passed ? 'Pass' : 'Fail'} />
        {result.remarks ? <DetailRow label="Remarks" value={result.remarks} /> : null}
      </Card>
    </ScreenLayout>
  );
}
