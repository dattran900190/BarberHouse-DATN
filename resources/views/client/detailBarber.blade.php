@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Chi tiết tin tức Baber House
@endsection

@section('content')
    <main class="container">
        <div class="main-detail-barber">
            <section class="bg-light">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 mb-4 mb-sm-5">
                            <div class="card card-style1 border-0">
                                <div class="card-body p-1-9 p-sm-2-3 p-md-6 p-lg-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6 mb-4 mb-lg-0">
                                            <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="...">
                                        </div>
                                        <div class="col-lg-6 px-xl-10">
                                            <div class="py-1-9 px-1-9 px-sm-6 mb-1-9 rounded">
                                                <h3 class="h2 text-dark mb-0">HỰ HẠ HỢ HẸ</h3>
                                                <span class="text-primary">Nhân viên</span>
                                            </div>
                                            <ul class="list-unstyled mb-1-9">
                                                <li class="mb-2 mb-xl-3 display-28">
                                                    <span class="display-26 text-secondary me-2 font-weight-600">
                                                        Kinh nghiệm:
                                                    </span>
                                                    10 Years
                                                </li>
                                                <li class="mb-2 mb-xl-3 display-28">
                                                    <span class="display-26 text-secondary me-2 font-weight-600">
                                                        Email:
                                                    </span>
                                                    edith@mail.com
                                                </li>
                                                <li class="mb-2 mb-xl-3 display-28">
                                                    <span class="display-26 text-secondary me-2 font-weight-600">
                                                        Số điện thoại:
                                                    </span>
                                                    0946576578
                                                </li>
                                                <li class="mb-2 mb-xl-3 display-28">
                                                    <span class="display-26 text-secondary me-2 font-weight-600">
                                                        Đánh giá:
                                                    </span>
                                                    5 <i class="fa-solid fa-star fa-2xs"></i>
                                                </li>
                                            </ul>
                                            <ul class="social-icon-style1 list-unstyled mb-0 ps-0">
                                                <li><a href="#!"><i class="ti-twitter-alt"></i></a></li>
                                                <li><a href="#!"><i class="ti-facebook"></i></a></li>
                                                <li><a href="#!"><i class="ti-pinterest"></i></a></li>
                                                <li><a href="#!"><i class="ti-instagram"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-4 mb-sm-5">
                            <div>
                                <h4 class="text-primary mb-3 mb-sm-4">Mô tả</h4>
                                <p>Edith is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                                    the
                                    industry's standard dummy text ever since the 1500s, when an unknown printer took a
                                    galley
                                    of type and scrambled it to make a type specimen book.</p>
                                <p class="mb-0">It is a long established fact that a reader will be distracted by the
                                    readable
                                    content of a page when looking at its layout. The point of using Lorem Ipsum is that it
                                    has
                                    a more-or-less normal distribution of letters, as opposed.</p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 mb-4 mb-sm-5">
                                    {{-- <div class="mb-4 mb-sm-5">
                                        <span class="text-primary mb-3 mb-sm-4">Skill</span>
                                        <div class="progress-text">
                                            <div class="row">
                                                <div class="col-6">Driving range</div>
                                                <div class="col-6 text-end">80%</div>
                                            </div>
                                        </div>
                                        <div class="custom-progress progress progress-medium mb-3" style="height: 4px;">
                                            <div class="animated custom-bar progress-bar slideInLeft bg-secondary"
                                                style="width:80%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="10"
                                                role="progressbar"></div>
                                        </div>
                                        <div class="progress-text">
                                            <div class="row">
                                                <div class="col-6">Short Game</div>
                                                <div class="col-6 text-end">90%</div>
                                            </div>
                                        </div>
                                        <div class="custom-progress progress progress-medium mb-3" style="height: 4px;">
                                            <div class="animated custom-bar progress-bar slideInLeft bg-secondary"
                                                style="width:90%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="70"
                                                role="progressbar"></div>
                                        </div>
                                        <div class="progress-text">
                                            <div class="row">
                                                <div class="col-6">Side Bets</div>
                                                <div class="col-6 text-end">50%</div>
                                            </div>
                                        </div>
                                        <div class="custom-progress progress progress-medium mb-3" style="height: 4px;">
                                            <div class="animated custom-bar progress-bar slideInLeft bg-secondary"
                                                style="width:50%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="70"
                                                role="progressbar"></div>
                                        </div>
                                        <div class="progress-text">
                                            <div class="row">
                                                <div class="col-6">Putting</div>
                                                <div class="col-6 text-end">60%</div>
                                            </div>
                                        </div>
                                        <div class="custom-progress progress progress-medium" style="height: 4px;">
                                            <div class="animated custom-bar progress-bar slideInLeft bg-secondary"
                                                style="width:60%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="70"
                                                role="progressbar"></div>
                                        </div>
                                    </div> --}}
                                    <div>
                                        <h4 class="text-primary mb-3 mb-sm-4">Đánh giá</h4>
                                        <div class="py-2">
                                            <div class="media mb-3">
                                                <div style="background-image: url(https://bootdey.com/img/Content/avatar/avatar2.png)"
                                                    class="media-object avatar avatar-md mr-3"></div>
                                                <div class="media-body">
                                                    <div class="media-heading"><small
                                                            class="float-right text-muted">12/12/12</small>
                                                        <h5>HẸ HẸ HẸ</h5>
                                                    </div>
                                                    <div class="text-muted text-small">Samsa was a travelling salesman - and
                                                        above it there hung a picture that he had recently cut out of an
                                                        illustrated magazine and housed in a nice, gilded frame.</div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="media mb-3">
                                                <div style="background-image: url(https://bootdey.com/img/Content/avatar/avatar2.png)"
                                                    class="media-object avatar avatar-md mr-3"></div>
                                                <div class="media-body">
                                                    <div class="media-heading"><small
                                                            class="float-right text-muted">12/12/12</small>
                                                        <h5>HẸ HẸ HẸ</h5>
                                                    </div>
                                                    <div class="text-muted text-small">Samsa was a travelling salesman - and
                                                        above it there hung a picture that he had recently cut out of an
                                                        illustrated magazine and housed in a nice, gilded frame.</div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="media mb-3">
                                                <div style="background-image: url(https://bootdey.com/img/Content/avatar/avatar2.png)"
                                                    class="media-object avatar avatar-md mr-3"></div>
                                                <div class="media-body">
                                                    <div class="media-heading"><small
                                                            class="float-right text-muted">12/12/12</small>
                                                        <h5>HẸ HẸ HẸ</h5>
                                                    </div>
                                                    <div class="text-muted text-small">Samsa was a travelling salesman - and
                                                        above it there hung a picture that he had recently cut out of an
                                                        illustrated magazine and housed in a nice, gilded frame.</div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="media mb-3">
                                                <div style="background-image: url(https://bootdey.com/img/Content/avatar/avatar2.png)"
                                                    class="media-object avatar avatar-md mr-3"></div>
                                                <div class="media-body">
                                                    <div class="media-heading"><small
                                                            class="float-right text-muted">12/12/12</small>
                                                        <h5>HẸ HẸ HẸ</h5>
                                                    </div>
                                                    <div class="text-muted text-small">Samsa was a travelling salesman - and
                                                        above it there hung a picture that he had recently cut out of an
                                                        illustrated magazine and housed in a nice, gilded frame.</div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="media mb-3">
                                                <div style="background-image: url(https://bootdey.com/img/Content/avatar/avatar2.png)"
                                                    class="media-object avatar avatar-md mr-3"></div>
                                                <div class="media-body">
                                                    <div class="media-heading"><small
                                                            class="float-right text-muted">12/12/12</small>
                                                        <h5>HẸ HẸ HẸ</h5>
                                                    </div>
                                                    <div class="text-muted text-small">Samsa was a travelling salesman - and
                                                        above it there hung a picture that he had recently cut out of an
                                                        illustrated magazine and housed in a nice, gilded frame.</div>
                                                </div>
                                            </div>

                                            <nav class="pagination" aria-label="Page navigation">
                                                <button class="page-btn prev" disabled>‹ Prev</button>
                                                <ul class="page-list">
                                                    <li><button class="page-number active">1</button></li>
                                                    <li><button class="page-number">2</button></li>
                                                    <li><button class="page-number">3</button></li>
                                                    <li><button class="page-number">4</button></li>
                                                    <li><span class="ellipsis">…</span></li>
                                                    <li><button class="page-number">10</button></li>
                                                </ul>
                                                <button class="page-btn next">Next ›</button>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <style>
            #mainNav {
                background-color: #000;
            }
        </style>
    </main>
@endsection

@section('card-footer')
    
@endsection

