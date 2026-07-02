<section class="holiday-home-listing" data-holiday-home-list>
    <div class="holiday-home-listing-head">
        <div>
            <p>Holiday homes</p>
            <h2>Find your stay</h2>
        </div>
        <label class="holiday-home-search">
            <span>Search holiday homes</span>
            <input type="search" placeholder="Search by name, area, bedrooms or guests" data-holiday-home-search>
        </label>
    </div>

    @if ($holidayHomes->isNotEmpty())
        <div class="holiday-home-grid" data-holiday-home-grid>
            @foreach ($holidayHomes as $holidayHome)
                @php($searchText = strtolower(implode(' ', [
                    $holidayHome->area_name,
                    $holidayHome->name,
                    $holidayHome->description,
                    $holidayHome->number_of_bedrooms . ' bedrooms',
                    $holidayHome->maximum_number_of_guests . ' guests',
                ])))
                <article class="holiday-home-card" data-holiday-home-card data-search-text="{{ $searchText }}">
                    @if ($holidayHome->image_url)
                        <img src="{{ $holidayHome->image_url }}" alt="{{ $holidayHome->name }}" loading="lazy" decoding="async">
                    @else
                        <div class="holiday-home-placeholder" aria-hidden="true">{{ strtoupper(substr($holidayHome->name, 0, 1)) }}</div>
                    @endif
                    <div class="holiday-home-card-body">
                        <span>{{ $holidayHome->area_name }}</span>
                        <h3>{{ $holidayHome->name }}</h3>
                        <dl>
                            <div>
                                <dt>Bedrooms</dt>
                                <dd>{{ $holidayHome->number_of_bedrooms }}</dd>
                            </div>
                            <div>
                                <dt>Max guests</dt>
                                <dd>{{ $holidayHome->maximum_number_of_guests }}</dd>
                            </div>
                        </dl>
                        @if ($holidayHome->description)
                            @php($hasLongDescription = mb_strlen($holidayHome->description) > 260)
                            <div @class(['holiday-home-description', 'is-collapsible' => $hasLongDescription]) data-holiday-home-description>
                                <p>{{ $holidayHome->description }}</p>
                            </div>
                            @if ($hasLongDescription)
                                <button class="holiday-home-more" type="button" data-holiday-home-more aria-expanded="false">Show more</button>
                            @endif
                        @endif
                        @if ($holidayHome->online_booking_link)
                            <a class="prime-button" href="{{ $holidayHome->online_booking_link }}" target="_blank" rel="noopener">Book online</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <p class="holiday-home-empty" data-holiday-home-empty hidden>No holiday homes match your search.</p>
    @else
        <p class="holiday-home-empty">Holiday homes will appear here once they are added in admin.</p>
    @endif
</section>
