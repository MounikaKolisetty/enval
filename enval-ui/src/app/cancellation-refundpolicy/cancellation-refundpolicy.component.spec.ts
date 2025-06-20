import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CancellationRefundpolicyComponent } from './cancellation-refundpolicy.component';

describe('CancellationRefundpolicyComponent', () => {
  let component: CancellationRefundpolicyComponent;
  let fixture: ComponentFixture<CancellationRefundpolicyComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CancellationRefundpolicyComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(CancellationRefundpolicyComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
