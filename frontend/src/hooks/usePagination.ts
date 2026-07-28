import { useState, useCallback } from 'react';

interface PaginationState {
  page: number;
  perPage: number;
}

export function usePagination(initialPerPage: number = 10) {
  const [pagination, setPagination] = useState<PaginationState>({
    page: 1,
    perPage: initialPerPage,
  });

  const setPage = useCallback((page: number) => {
    setPagination((prev) => ({ ...prev, page }));
  }, []);

  const setPerPage = useCallback((perPage: number) => {
    setPagination({ page: 1, perPage });
  }, []);

  const goToNextPage = useCallback(() => {
    setPagination((prev) => ({ ...prev, page: prev.page + 1 }));
  }, []);

  const goToPreviousPage = useCallback(() => {
    setPagination((prev) => ({ ...prev, page: Math.max(1, prev.page - 1) }));
  }, []);

  const reset = useCallback(() => {
    setPagination({ page: 1, perPage: initialPerPage });
  }, [initialPerPage]);

  return {
    ...pagination,
    setPage,
    setPerPage,
    goToNextPage,
    goToPreviousPage,
    reset,
  };
}
