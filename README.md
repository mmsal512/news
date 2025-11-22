# News Website Project

A comprehensive news aggregation website specializing in artificial intelligence news, general news, and more, that automatically generates content through APIs.

## Features

### Core Functionality
- ✅ **Automatic News Fetching**: Hourly automated news fetching from GNews API and NewsAPI
- ✅ **Content Management**: Full admin panel for article management
- ✅ **Advanced Search**: Powerful search functionality with filters
- ✅ **Automatic Categorization**: AI-powered automatic article categorization
- ✅ **AI Ranking System**: Intelligent ranking based on relevance, freshness, engagement, and quality
- ✅ **Viewership Statistics**: Track article views and unique views
- ✅ **Multi-language Support**: English and Arabic (RTL support)
- ✅ **SEO Optimized**: Sitemap, meta tags, structured data, and Open Graph
- ✅ **Copyright Protection**: Attribution and source links for all articles

### Technical Features
- ✅ **Queue Jobs**: Background processing for news fetching
- ✅ **Caching System**: Performance optimization with intelligent caching
- ✅ **Content Deduplication**: Prevents duplicate articles
- ✅ **Responsive Design**: Mobile-friendly interface with Tailwind CSS
- ✅ **Real-time Updates**: Livewire components for dynamic interactions

## Requirements

- PHP 8.2+
- Laravel 11.x (or 12.x when available)
- MySQL 8.0+
- Composer
- Node.js & NPM

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd news
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure environment variables**
   Edit `.env` and add your API keys:
   ```env
   GNEWS_API_KEY=your_gnews_api_key_here
   NEWSAPI_API_KEY=your_newsapi_key_here
   
   DB_DATABASE=news
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed default data**
   ```bash
   php artisan db:seed
   ```

8. **Build assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## Configuration

### Queue Configuration

For production, set up a queue worker:
```bash
php artisan queue:work
```

Or use Supervisor for automatic queue processing.

### Scheduled Tasks

The application uses Laravel's task scheduler. Add this to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This will run:
- Hourly news fetching
- Daily cleanup of old articles (2 AM)

### API Rate Limits

Configure rate limits in `.env`:
```env
GNEWS_RATE_LIMIT=1000
NEWSAPI_RATE_LIMIT=100
```

## Usage

### Fetching News

**Manual fetch:**
```bash
php artisan news:fetch
```

**Fetch specific type:**
```bash
php artisan news:fetch --type=ai
php artisan news:fetch --type=tech
php artisan news:fetch --type=general
```

### Admin Panel

Access the admin panel at `/admin/articles` after logging in.

Features:
- View all articles
- Create/edit/delete articles
- Publish/unpublish articles
- Bulk actions (publish, unpublish, delete, categorize)
- Filter by status, category, language
- Search articles

### Public Routes

- `/` - Home page (redirects to articles)
- `/articles` - Article listing with search and filters
- `/articles/{id}` - Article detail page
- `/search` - Search page
- `/sitemap.xml` - Sitemap index
- `/sitemap-articles.xml` - Articles sitemap

## Project Structure

```
app/
├── Console/Commands/        # Artisan commands
├── Http/
│   ├── Controllers/        # Application controllers
│   │   ├── Admin/          # Admin controllers
│   │   └── ArticleController.php
│   └── Middleware/         # Custom middleware
├── Jobs/                   # Queue jobs
├── Models/                 # Eloquent models
├── Services/              # Business logic services
│   ├── Cache/             # Caching services
│   ├── Content/           # Content processing
│   ├── News/              # News API services
│   └── Search/            # Search services
└── View/Components/        # Blade components

resources/
├── views/
│   ├── admin/             # Admin views
│   ├── articles/          # Public article views
│   └── components/        # Reusable components
└── lang/                  # Language files

database/
├── migrations/            # Database migrations
└── seeders/               # Database seeders
```

## API Integration

### GNews API
- Primary news source
- Supports multiple languages
- Rate limit: 1000 requests/hour (configurable)

### NewsAPI
- Secondary/backup source
- Rate limit: 100 requests/hour (configurable)

## Features in Detail

### Automatic Categorization
Articles are automatically categorized based on content analysis using keyword matching and scoring algorithms.

### AI Ranking System
Articles are ranked based on:
- **Relevance** (30%): AI keywords, category match
- **Freshness** (25%): Publication date
- **Engagement** (20%): Views and unique views
- **Quality** (15%): Content length, summary, images, meta
- **Source Reliability** (10%): Source reliability score

### SEO Features
- XML Sitemap generation
- Meta tags (Open Graph, Twitter Cards)
- Structured data (JSON-LD)
- SEO-friendly URLs with slugs
- Meta descriptions

### Copyright Protection
- Automatic attribution to sources
- Copyright notices
- Links to original articles
- Configurable attribution requirements

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

## Troubleshooting

### Queue not processing
Make sure queue worker is running:
```bash
php artisan queue:work
```

### News not fetching
1. Check API keys in `.env`
2. Verify API rate limits
3. Check logs: `storage/logs/laravel.log`

### Cache issues
Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please check the project documentation or create an issue in the repository.
