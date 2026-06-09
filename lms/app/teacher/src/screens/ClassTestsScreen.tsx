import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import ListRow from '../components/ListRow';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';
import { useStaleLoad } from '../hooks/useStaleLoad';
import { useRefreshOnFocus } from '../hooks/useRefreshOnFocus';

export default function ClassTestsScreen() {
  const navigation = useNavigation<any>();
  const { loading, refreshing, beginLoad, endLoad, markHasData } = useStaleLoad();
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(
    async (options?: { showRefresh?: boolean }) => {
      beginLoad(options);
      try {
        const res: any = await LmsApi.classTests();
        setItems(res.tests ?? []);
        markHasData();
      } finally {
        endLoad();
      }
    },
    [beginLoad, endLoad, markHasData],
  );

  useRefreshOnFocus(() => load());

  return (
    <ScreenLayout
      title="Class Tests"
      subtitle="Create & enter marks"
      onBack={() => navigation.navigate('WorkHub')}
      refreshing={refreshing}
      onRefresh={() => load({ showRefresh: true })}>
      <Card>
        <Text style={styles.hint}>
          Tap a test to enter marks. Use “Create class test” from the Work tab to add a new one.
        </Text>
      </Card>

      <Card title={`All tests (${items.length})`}>
        <DataList
          loading={loading}
          items={items}
          emptyText="No class tests yet"
          renderItem={(t: any) => {
            const title = String(t.test_name ?? 'Untitled test');
            const course = String(t.course_name ?? '').trim();
            const subject = String(t.subject_name ?? '').trim();
            const date = String(t.test_date ?? '').trim();
            const subtitle = [course, subject, date].filter(Boolean).join(' · ');

            return (
              <ListRow
                title={title}
                subtitle={subtitle}
                onPress={() =>
                  navigation.navigate('EnterMarks', {
                    courseId: t.course_id != null ? String(t.course_id) : undefined,
                    subjectId: t.subject_id != null ? String(t.subject_id) : undefined,
                    testId: t.id != null ? String(t.id) : undefined,
                  })
                }
                right="Enter"
              />
            );
          }}
        />
        {!loading && items.length > 0 ? <View style={styles.footerSpace} /> : null}
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hint: { color: theme.muted, fontSize: 13, lineHeight: 18 },
  footerSpace: { height: 4 },
});
