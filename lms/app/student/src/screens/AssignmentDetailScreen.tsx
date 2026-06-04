import React, { useCallback, useState } from 'react';
import { Text, Pressable, StyleSheet, Alert, Share, ActivityIndicator } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import ScreenLayout from '../components/ScreenLayout';
import { Card } from '../components/Card';
import DetailRow from '../components/DetailRow';
import { LmsApi } from '../api/lms';
import { PRIMARY } from '../config';
import { AssignmentsStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<AssignmentsStackParamList, 'AssignmentDetail'>;

export default function AssignmentDetailScreen({ navigation, route }: Props) {
  const { id } = route.params;
  const [loading, setLoading] = useState(true);
  const [item, setItem] = useState<any>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res: any = await LmsApi.assignment(id);
      setItem(res.assignment);
    } catch (e: any) {
      Alert.alert('Error', e?.message ?? 'Could not load assignment');
    } finally {
      setLoading(false);
    }
  }, [id]);

  React.useEffect(() => {
    load();
  }, [load]);

  return (
    <ScreenLayout
      title="Assignment"
      subtitle={item?.document_name ?? 'Details'}
      onBack={() => navigation.goBack()}
      refreshing={loading}
      onRefresh={load}>
      {loading && !item ? <ActivityIndicator color={PRIMARY} style={{ marginTop: 24 }} /> : null}
      {item ? (
        <Card>
          <DetailRow label="Document name" value={item.document_name} />
          <DetailRow label="Batch" value={item.batch_name} />
          <DetailRow label="Type" value={item.type} />
          {item.type === 'link' ? (
            <>
              <DetailRow label="Link" value={item.link_url} />
              <Pressable
                style={styles.btn}
                onPress={() => Share.share({ message: item.link_url, title: item.document_name })}>
                <Text style={styles.btnText}>Share link</Text>
              </Pressable>
            </>
          ) : (
            <>
              <DetailRow label="File" value={item.document} />
              <Pressable
                style={styles.btn}
                onPress={() => navigation.navigate('AssignmentFile', { id })}>
                <Text style={styles.btnText}>View file in app</Text>
              </Pressable>
            </>
          )}
        </Card>
      ) : null}
    </ScreenLayout>
  );
}

const styles = StyleSheet.create({
  btn: { backgroundColor: PRIMARY, paddingVertical: 14, borderRadius: 8, alignItems: 'center', marginTop: 8 },
  btnText: { color: '#fff', fontWeight: '700' },
});
