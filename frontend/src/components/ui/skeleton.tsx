import { cn } from '@/utils/cn';

interface SkeletonProps {
  className?: string;
}

function Skeleton({ className }: SkeletonProps) {
  return <div className={cn('animate-pulse rounded-md bg-primary/10', className)} />;
}

export { Skeleton };
