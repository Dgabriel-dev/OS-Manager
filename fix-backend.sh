#!/bin/bash
# Script to apply backend fixes
set -e

BASEDIR="$(cd "$(dirname "$0")" && pwd)"

# Copy routes
cp /tmp/api.php "$BASEDIR/backend/routes/api.php"

# Copy CORS
cp /tmp/cors.php "$BASEDIR/backend/config/cors.php"

# Fix ownership
chown www-data:www-data "$BASEDIR/backend/routes/api.php" "$BASEDIR/backend/config/cors.php"

# Add history method to ServiceOrderController
if ! grep -q 'function history' "$BASEDIR/backend/app/Http/Controllers/Api/ServiceOrderController.php"; then
  # Insert before the last closing brace
  sed -i '$i\
\
    public function history(int $id): JsonResponse\
    {\
        $order = \App\Models\ServiceOrder::find($id);\
\
        if (!$order) {\
            return response()->json(['message' => '\''Ordem de serviço não encontrada.'\''], 404);\
        }\
\
        return response()->json([\
            '\''data'\'' => \App\Http\Resources\OrderHistoryResource::collection(\
                $order->histories()->with('\''user'\'')->latest()->get()\
            ),\
        ]);\
    }' "$BASEDIR/backend/app/Http/Controllers/Api/ServiceOrderController.php"
  chown www-data:www-data "$BASEDIR/backend/app/Http/Controllers/Api/ServiceOrderController.php"
fi

# Add indexCategories to StockController
if ! grep -q 'function indexCategories' "$BASEDIR/backend/app/Http/Controllers/Api/StockController.php"; then
  sed -i '$i\
\
    public function indexCategories(): JsonResponse\
    {\
        $categories = \App\Models\StockCategory::orderBy('\''name'\'')->get();\
\
        return response()->json([\
            '\''data'\'' => $categories,\
        ]);\
    }' "$BASEDIR/backend/app/Http/Controllers/Api/StockController.php"
  chown www-data:www-data "$BASEDIR/backend/app/Http/Controllers/Api/StockController.php"
fi

# Add indexCategories to FinancialController
if ! grep -q 'function indexCategories' "$BASEDIR/backend/app/Http/Controllers/Api/FinancialController.php"; then
  sed -i '$i\
\
    public function indexCategories(): JsonResponse\
    {\
        $categories = \App\Models\FinancialCategory::orderBy('\''name'\'')->get();\
\
        return response()->json([\
            '\''data'\'' => $categories,\
        ]);\
    }' "$BASEDIR/backend/app/Http/Controllers/Api/FinancialController.php"
  chown www-data:www-data "$BASEDIR/backend/app/Http/Controllers/Api/FinancialController.php"
fi

# Clear caches
docker compose exec php php artisan route:clear 2>/dev/null || true
docker compose exec php php artisan config:clear 2>/dev/null || true

echo "Backend fixes applied successfully!"
echo "Routes, CORS, and controller methods updated."
