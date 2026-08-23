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
                @php
                    $searchText = strtolower(implode(' ', [
                        $holidayHome->area_name,
                        $holidayHome->name,
                        $holidayHome->description,
                        $holidayHome->number_of_bedrooms . ' bedrooms',
                        $holidayHome->maximum_number_of_guests . ' guests',
                    ]));
                @endphp
                <article class="holiday-home-card" data-holiday-home-card data-holiday-home-open="holiday-home-modal-{{ $holidayHome->id }}" data-search-text="{{ $searchText }}" tabindex="0">
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
                            @php
                                $hasLongDescription = mb_strlen($holidayHome->description) > 260;
                            @endphp
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
                        <button class="holiday-home-details-button" type="button" data-holiday-home-open="holiday-home-modal-{{ $holidayHome->id }}">View details</button>
                    </div>
                </article>
            @endforeach
        </div>

        @foreach ($holidayHomes as $holidayHome)
            @php
                $detailBlock = ($holidayHomeDetails ?? collect())->get(strtolower(trim($holidayHome->name))) ?? [];
                $detailDescription = data_get($detailBlock, "body.$locale", data_get($detailBlock, 'body.en', $holidayHome->description));
                $detailImageItems = array_values(array_filter(data_get($detailBlock, 'images', [])));

                if ($holidayHome->image_url) {
                    array_unshift($detailImageItems, $holidayHome->image_url);
                }

                $detailImages = collect($detailImageItems)->unique()->values();
            @endphp
            <div class="holiday-home-modal" id="holiday-home-modal-{{ $holidayHome->id }}" data-holiday-home-modal aria-hidden="true">
                <button class="holiday-home-modal-backdrop" type="button" data-holiday-home-close aria-label="Close details"></button>
                <section class="holiday-home-modal-panel" role="dialog" aria-modal="true" aria-labelledby="holiday-home-modal-title-{{ $holidayHome->id }}">
                    <div class="holiday-home-modal-head">
                        <p>{{ $holidayHome->area_name }}</p>
                        <h2 id="holiday-home-modal-title-{{ $holidayHome->id }}">{{ $holidayHome->name }}</h2>
                        <button type="button" data-holiday-home-close aria-label="Close details">Close</button>
                    </div>

                    @if ($detailImages->isNotEmpty())
                        <div class="holiday-home-slider" data-holiday-home-slider>
                            <div class="holiday-home-slider-frame">
                                @foreach ($detailImages as $image)
                                    <img
                                        @class(['is-active' => $loop->first])
                                        src="{{ $image }}"
                                        alt="{{ $holidayHome->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        data-holiday-slide>
                                @endforeach
                                @if ($detailImages->count() > 1)
                                    <button class="holiday-home-slider-arrow is-prev" type="button" data-holiday-prev aria-label="Previous image">‹</button>
                                    <button class="holiday-home-slider-arrow is-next" type="button" data-holiday-next aria-label="Next image">›</button>
                                @endif
                            </div>
                            @if ($detailImages->count() > 1)
                                <div class="holiday-home-slider-meta">
                                    <span data-holiday-counter>1 / {{ $detailImages->count() }}</span>
                                </div>
                                <div class="holiday-home-slider-thumbs">
                                    @foreach ($detailImages as $image)
                                        <button @class(['is-active' => $loop->first]) type="button" data-holiday-thumb="{{ $loop->index }}" aria-label="Show image {{ $loop->iteration }}">
                                            <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="holiday-home-modal-summary">
                        <dl class="holiday-home-modal-facts">
                            <div>
                                <dt>Bedrooms</dt>
                                <dd>{{ $holidayHome->number_of_bedrooms }}</dd>
                            </div>
                            <div>
                                <dt>Max guests</dt>
                                <dd>{{ $holidayHome->maximum_number_of_guests }}</dd>
                            </div>
                        </dl>

                        @if ($detailDescription)
                            <div class="prime-open-copy">{!! nl2br(e($detailDescription)) !!}</div>
                        @endif

                        @if ($holidayHome->online_booking_link)
                            <div class="prime-button-row">
                                <a class="prime-button" href="{{ $holidayHome->online_booking_link }}" target="_blank" rel="noopener">Book online</a>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        @endforeach
        <p class="holiday-home-empty" data-holiday-home-empty hidden>No holiday homes match your search.</p>
    @else
        <p class="holiday-home-empty">Holiday homes will appear here once they are added in admin.</p>
    @endif
</section>
