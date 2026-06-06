import { useCallback } from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { useStudentContext } from './useStudentContext';

export function useRefreshStudentOnFocus() {
  const { refresh } = useStudentContext();

  useFocusEffect(
    useCallback(() => {
      refresh();
    }, [refresh]),
  );
}
