import React, { useCallback, useState } from 'react';
import { Text, StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { SectionTitle } from '../components/DashboardUi';
import {
  AssignmentListItem,
  AssignmentsSummaryCard,
} from '../components/AssignmentUi';
import { LmsApi } from '../api/lms';
import { theme } from '../ui/theme';
import { AssignmentsStackParamList } from '../navigation/types';

export default function AssignmentsScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<AssignmentsStackParamList>>();
  const [loading, setLoading] = useState(true);
  const [items, setItems] = useState<any[]>([]);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.assignments();
      setItems(res.assignments ?? []);
    } finally {
      setLoading(false);
    }
  }, []);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Assignments"
      subtitle="Active for your batch"
      onBack={() => navigation.navigate('AssignmentsHub')}
      refreshing={loading}
      onRefresh={load}>
      <AssignmentsSummaryCard count={items.length} />

      <SectionTitle>All assignments</SectionTitle>
      <View style={styles.listCard}>
        {items.length > 0 ? (
          items.map((item, index) => (
            <View key={item.id ?? index}>
              <AssignmentListItem
                item={item}
                onPress={() => navigation.navigate('AssignmentDetail', { id: item.id })}
              />
              {index < items.length - 1 ? <View style={styles.divider} /> : null}
            </View>
          ))
        ) : (
          <Text style={styles.empty}>No assignments for your batch</Text>
        )}
      </View>
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  listCard: {
    backgroundColor: theme.card,
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 4,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: theme.border,
    shadowColor: theme.shadow,
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.06,
    shadowRadius: 14,
    elevation: 3,
  },
  divider: {
    height: StyleSheet.hairlineWidth,
    backgroundColor: theme.border,
  },
  empty: {
    fontSize: 14,
    color: theme.muted,
    textAlign: 'center',
    paddingVertical: 24,
  },
});
