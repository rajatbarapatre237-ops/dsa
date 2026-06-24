import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View, Pressable } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import { DataList } from '../components/DataList';
import AppIcon from '../components/AppIcon';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { theme } from '../ui/theme';
import { platformWeight } from '../ui/typography';
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

  useRefreshOnFocus(load);

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
            const isLast = items.indexOf(t) === items.length - 1;

            return (
              <View style={[styles.item, isLast && styles.itemLast]}>
                <Text style={[styles.itemTitle, platformWeight('600')]}>{title}</Text>
                {subtitle ? <Text style={styles.itemSub}>{subtitle}</Text> : null}
                <View style={styles.actionRow}>
                  <Pressable
                    style={styles.actionBtn}
                    onPress={() =>
                      navigation.navigate('CreateClassTest', { testId: String(t.id) })
                    }>
                    <AppIcon name="create-outline" size={16} color={PRIMARY} />
                    <Text style={[styles.actionText, platformWeight('700')]}>Edit</Text>
                  </Pressable>
                  <Pressable
                    style={styles.actionBtn}
                    onPress={() =>
                      navigation.navigate('EnterMarks', {
                        courseId: t.course_id != null ? String(t.course_id) : undefined,
                        subjectId: t.subject_id != null ? String(t.subject_id) : undefined,
                        testId: t.id != null ? String(t.id) : undefined,
                      })
                    }>
                    <AppIcon name="clipboard-outline" family="ionicons" size={16} color={PRIMARY} />
                    <Text style={[styles.actionText, platformWeight('700')]}>Enter marks</Text>
                  </Pressable>
                </View>
              </View>
            );
          }}
        />
      </Card>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  hint: { color: theme.muted, fontSize: 13, lineHeight: 20 },
  item: {
    paddingTop: 14,
    paddingBottom: 12,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: theme.border,
  },
  itemLast: {
    borderBottomWidth: 0,
    paddingBottom: 0,
  },
  itemTitle: {
    fontSize: 15,
    color: theme.text,
  },
  itemSub: {
    fontSize: 12,
    color: theme.muted,
    marginTop: 4,
    lineHeight: 17,
  },
  actionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
    gap: 20,
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: theme.border,
  },
  actionBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingVertical: 2,
  },
  actionText: { color: PRIMARY, fontSize: 13 },
});
