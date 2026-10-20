<footer class="site-footer">
    <div class="container">
        © {{ date('Y') }} {{ config('app.name') }}
        <span class="query-count">Запросов к БД: {{ count(\Illuminate\Support\Facades\DB::getQueryLog()) }}</span>
    </div>
</footer>
