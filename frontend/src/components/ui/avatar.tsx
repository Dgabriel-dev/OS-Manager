import { cn } from '@/utils/cn';

interface AvatarProps {
  src?: string | null;
  alt?: string;
  fallback?: string;
  className?: string;
  size?: 'sm' | 'md' | 'lg';
}

function Avatar({ src, alt, fallback, className, size = 'md' }: AvatarProps) {
  const sizeClasses = {
    sm: 'h-8 w-8 text-xs',
    md: 'h-10 w-10 text-sm',
    lg: 'h-12 w-12 text-base',
  };

  if (src) {
    return (
      <img
        src={src}
        alt={alt || ''}
        className={cn('rounded-full object-cover', sizeClasses[size], className)}
      />
    );
  }

  return (
    <div
      className={cn(
        'relative flex items-center justify-center rounded-full bg-muted font-medium text-muted-foreground',
        sizeClasses[size],
        className
      )}
    >
      {fallback || '?'}
    </div>
  );
}

export { Avatar };
